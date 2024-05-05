<?php

require_once "AppController.php";
require_once __DIR__ . '/../models/Quest.php';
require_once __DIR__ . '/../repository/QuestRepository.php';
require_once __DIR__ . '/../repository/QuestStatisticsRepository.php';
require_once __DIR__ . '/../repository/QuestionsRepository.php';
require_once __DIR__ . '/../repository/OptionsRepository.php';
require_once __DIR__ . '/../repository/WalletRepository.php';
require_once __DIR__ . '/../services/QuestAuthorizationService.php';
require_once __DIR__ . '/../services/QuestService.php';
require_once __DIR__ . '/../exceptions/Quests.php';

class QuestsController extends AppController
{
  private $questRepository;
  private $walletRepository;
  private $questAuthorizationService;
  private $questionsRepository;
  private $questService;
  private $optionsRepository;

  public function __construct()
  {
    parent::__construct();
    $this->questRepository = new QuestRepository();
    $this->questionsRepository = new QuestionsRepository();
    $this->optionsRepository = new OptionsRepository();
    $this->walletRepository = new WalletRepository();
    $this->questAuthorizationService = new QuestAuthorizationService();
    $this->questService = new QuestService();
  }


  // returns quests which are approved by some general admin
  public function index()
  {
    $quests = $this->questRepository->getApprovedQuests();
    $this->render('layout', ['title' => 'quest list', 'quests' => $quests], 'quests');
  }

  // create quest returns quest data given by questId param and checks access to given route
  public function createQuest(?int $questId = null)
  {
    try {
      $requestType = $questId ? QuestAuthorizeRequest::EDIT : QuestAuthorizeRequest::CREATE;

      $this->questAuthorizationService->authorizeQuestAction($requestType, $questId);

      $quest = $this->questService->getQuestWithQuestionsAndOptions($questId);

      $this->render('layout', [
        'title' => 'quest add',
        'quest' => $quest
      ], 'createQuest');
    } catch (NotLoggedInException $e) {
      $this->redirectWithParams('login', ['message' => 'first, you need to log in']);
    } catch (Exception $e) {
      $this->redirectWithParams('error', ['message' => $e->getMessage()]);
    }
  }

  public function editQuest(?int $questId = null)
  {
    try {
      $this->questAuthorizationService->authorizeQuestAction(QuestAuthorizeRequest::EDIT, $questId);

      $quizData = array(
        "quizTitle" => $_POST["quizTitle"],
        "quizDescription" => $_POST["quizDescription"],
        "requiredWallet" => $_POST["requiredWallet"],
        "timeRequired" => $_POST["timeRequired"],
        "expiryDate" => $_POST["expiryDate"],
        "participantsLimit" => $_POST["participantsLimit"],
        "poolAmount" => $_POST["poolAmount"]
      );


      foreach ($_POST["questions"] as $questionId => $questionData) {
        $options = [];
        $correctOptionsCount = 0;
        $type = QuestionType::UNKNOWN;

        if (isset($_POST["options"][$questionId])) {
          foreach ($_POST["options"][$questionId] as $optionIndex => $optionData) {
            $isCorrect = isset($optionData['isCorrect']);
            if ($isCorrect) {
              $correctOptionsCount++;
            }
            $options[] = new Option($optionIndex, $questionId, $optionData['text'], $isCorrect);
          }

          if ($correctOptionsCount == 1) {
            $type = QuestionType::SINGLE_CHOICE;
          } else if ($correctOptionsCount == 0) {
            throw new Exception('baaad');
          } else {
            $type = QuestionType::MULTIPLE_CHOICE;
          }
        } else {
          $type = QuestionType::READ_TEXT;
        }

        $question = new Question($questionId, $questId, $questionData['text'], $type);
        $question->setOptions($options);
      }

    } catch (Exception $e) {

    } catch (NotLoggedInException $e) {
      $this->redirectWithParams('login', ['message' => 'first, you need to log in']);
    }
  }

  public function enterQuest($questId)
  {
    try {
      $this->questAuthorizationService->authorizeQuestAction(QuestAuthorizeRequest::ENTER, $questId);

      $userId = $this->sessionService->get('user')['id'];

      if ($this->request->isGet()) {
        $this->renderStartQuest($userId, $questId);
      } else {
        $this->startQuest($userId, $questId);
      }

    } catch (NotLoggedInException $e) {
      $this->redirectWithParams('login', ['message' => 'first, you need to log in']);
    } catch (Exception $e) {
      $this->redirectWithParams('error', ['message' => $e->getMessage()]);
    }
  }

  private function renderStartQuest($userId, $questId)
  {
    $quest = $this->questRepository->getQuestById($questId);
    $wallets = $this->walletRepository->getBlockchainWallets($userId, $quest->getRequiredWallet());

    $this->render('enterQuest', ['title' => 'enter quest', 'wallets' => $wallets]);
  }

  private function startQuest($userId, $questId)
  {
    $walletSelect = $this->request->post('walletSelect');

    if (!$walletSelect) {
      $this->redirectWithParams('error', ['message' => 'something went wrong', 'code' => 404]);
    }

    $walletId = $walletSelect;

    if ($walletSelect === 'new') {
      $newWalletAddress = $this->request->post('newWalletAddress');

      if (!$newWalletAddress) {
        $this->redirectWithParams('error', ['message' => 'something went wrong', 'code' => 404]);
      }

      $walletId = $this->addNewWallet($userId, $questId, $newWalletAddress);
    }

    $this->startQuestWithWallet($walletId, $questId);
  }

  private function startQuestWithWallet($walletId, $questId)
  {
    $quest = $this->questRepository->getQuestById($questId);

    SessionService::set('walletId', $walletId);
    SessionService::set('questId', $questId);
    SessionService::set('questPoints', $quest->getPoints());

    $this->redirect('gameplay');
  }

  private function addNewWallet($userId, $questId, $walletAddress): int
  {
    $quest = $this->questRepository->getQuestById($questId);
    $wallet = new Wallet(0, $userId, $quest->getRequiredWallet(), $walletAddress, date('Y-m-d'), date('Y-m-d'));
    $walletId = $this->walletRepository->addWallet($wallet);

    return $walletId;
  }
}