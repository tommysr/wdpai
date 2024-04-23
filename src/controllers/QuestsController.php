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

    return $this->renderQuestion($questions[0]);
  }

  private function renderQuestion(Question $question)
  {
    $question_type = $question->getType();
    $options = $this->optionsRepository->getOptionsByQuestionId($question->getQuestionId());

    switch ($question_type) {
      case QuestionType::SingleChoice:
        $this->renderSingleChoiceQuestion($question, $options);
        break;
      case QuestionType::MultipleChoice:
        $this->renderMultipleChoiceQuestion($question, $options);
        break;
      default:
        $this->renderReadTextQuestion($question);
    }
  }

  private function renderSingleChoiceQuestion($question, $options)
  {

  }

  private function renderMultipleChoiceQuestion($question, $options)
  {

  }

  private function renderReadTextQuestion($question)
  {

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


  public function enterQuest($quest_id)
  {
    session_start();

    $user_id = $_SESSION['user_id'];

    if (!isset($user_id)) {
      $url = "http://$_SERVER[HTTP_HOST]";
      header("Location: {$url}/login");
      return;
    }

    if (!$this->userCanEnterQuest($user_id, $quest_id)) {
      return $this->quests();
    }
    

    $quest = $this->questRepository->getQuestById($quest_id);

    if (!$quest) {
      return $this->quests();
    }

    $wallets = $this->walletRepository->getBlockchainWallets($user_id, $quest->getRequiredWallet());

    if (!$this->isPost()) {
      return $this->render('enterQuest', ['title' => 'enter quest', 'wallets' => $wallets]);
    }


    // $wallet_address = $this->walletAddressRepository->find($user_id);

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