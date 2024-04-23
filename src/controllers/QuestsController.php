<?php

require_once "AppController.php";
require_once __DIR__ . '/../models/Quest.php';
require_once __DIR__ . '/../repository/QuestRepository.php';
require_once __DIR__ . '/../repository/QuestStatisticsRepository.php';
require_once __DIR__ . '/../repository/QuestionsRepository.php';
require_once __DIR__ . '/../repository/OptionsRepository.php';
require_once __DIR__ . '/../repository/WalletRepository.php';

class QuestsController extends AppController
{
  private $questRepository;
  private $questStatisticsRepository;
  private $questionsRepository;
  private $optionsRepository;
  private $walletRepository;

  public function __construct()
  {
    parent::__construct();
    $this->questRepository = new QuestRepository();
    $this->questStatisticsRepository = new QuestStatisticsRepository();
    $this->questionsRepository = new QuestionsRepository();
    $this->optionsRepository = new OptionsRepository();
    $this->walletRepository = new WalletRepository();
  }

  public function quests()
  {
    $quests = $this->questRepository->getQuests();
    $this->render('quests', ['title' => 'quest list', 'quests' => $quests]);
  }

  public function startQuest(int $questId)
  {

    $quest = $this->questRepository->getQuestById($questId);

    if ($quest === null) {
      throw new Exception('given quest does not exist in database');
    }

    $questions = $this->questionsRepository->getQuestionsByQuestId($questId);

    if (count($questions) === 0) {
      throw new Exception('no questions in database');
    }

    return $this->renderQuestion($questions[0]);
  }

  private function renderQuestion(Question $question)
  {
    $question_type = $question->getType();
    $options = $this->optionsRepository->getOptionsByQuestionId($question->getQuestionId());
    
    switch ($question_type->getValue()) {
      case QuestionType::SINGLE_CHOICE:
        $this->renderSingleChoiceQuestion($question, $options);
        break;
      case QuestionType::MULTIPLE_CHOICE:
        $this->renderMultipleChoiceQuestion($question, $options);
        break;
      default:
        $this->renderReadTextQuestion($question);
    }
  }

  private function renderSingleChoiceQuestion($question, $options)
  {
    echo "single choice";
  }

  private function renderMultipleChoiceQuestion($question, $options)
  {
    echo "multiple choice";
  }

  private function renderReadTextQuestion($question)
  {
    echo "text read";
  }


  public function processUserResponse($questionID, $selectedOptions)
  {
    // // Check user's response against the correct answer (if applicable)
    // // Update user's score and store their response

    // // Determine next question (e.g., get next question from database)
    // $nextQuestion = $this->questionRepository->getNextQuestion($questionID);

    // if ($nextQuestion) {
    //     // Render next question view
    //     $this->renderQuestion($nextQuestion);
    // } else {
    //     // End quiz
    //     $this->endQuiz();
    // }
  }

  private function redirectToQuests()
  {
    $url = "http://$_SERVER[HTTP_HOST]";
    header("Location: {$url}/quests");
  }

  private function redirectToLogin()
  {
    $url = "http://$_SERVER[HTTP_HOST]";
    header("Location: {$url}/login");
  }


  public function enterQuest($quest_id)
  {
    session_start();

    $user_id = $_SESSION['user_id'];

    if (!isset($user_id)) {
      return $this->redirectToLogin();
    }

    $quest = $this->questRepository->getQuestById($quest_id);

    if (!$this->userCanEnterQuest($user_id, $quest_id) || !$quest) {
      return $this->redirectToQuests();
    }

    if (!$this->isPost()) {
      $wallets = $this->walletRepository->getBlockchainWallets($user_id, $quest->getRequiredWallet());

      return $this->render('enterQuest', ['title' => 'enter quest', 'wallets' => $wallets]);
    } else {
      $walletSelect = $_POST['walletSelect'];
      if (!isset($walletSelect)) {
        return $this->redirectToQuests();
      }

      if ($walletSelect === 'new') {
        $newWalletAddress = $_POST['newWalletAddress'];

        if (isset($newWalletAddress)) {

          $newWalletAddress = $_POST['newWalletAddress'];
          $wallet = new Wallet(0, $user_id, $quest->getRequiredWallet(), $newWalletAddress, date('Y-m-d'), date('Y-m-d'));
          $walletId = $this->walletRepository->addWallet($wallet);

          echo $walletId;
          $_SESSION['wallet_id'] = $walletId;

        } else {
          return $this->redirectToQuests();
        }
      } else {
        $_SESSION['wallet_id'] = $walletSelect;
      }
    }

    $this->startQuest($quest_id);
  }

  private function userCanEnterQuest($userId, $questId)
  {
    if ($this->questStatisticsRepository->getQuestStatistic($userId, $questId)) {
      return false;
    }

    return true;
  }
}