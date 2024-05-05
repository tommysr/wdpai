<?php

require_once "AppController.php";
require_once __DIR__ . '/../models/Quest.php';
require_once __DIR__ . '/../repository/QuestRepository.php';
require_once __DIR__ . '/../repository/QuestStatisticsRepository.php';
require_once __DIR__ . '/../repository/QuestionsRepository.php';
require_once __DIR__ . '/../repository/OptionsRepository.php';
require_once __DIR__ . '/../repository/WalletRepository.php';
require_once __DIR__ . '/../services/QuestAuthorizationService.php';
require_once __DIR__ . '/../exceptions/Quests.php';

class QuestsController extends AppController
{
  private $questRepository;
  private $walletRepository;
  private $questAuthorizationService;
  private $questionsRepository;

  private $optionsRepository;

  public function __construct()
  {
    parent::__construct();
    $this->questRepository = new QuestRepository();
    $this->questionsRepository = new QuestionsRepository();
    $this->optionsRepository = new OptionsRepository();
    $this->walletRepository = new WalletRepository();
    $this->questAuthorizationService = new QuestAuthorizationService();
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
    if ($questId) {
      // unauthorized if user is not creator of given quest
      // redirects if access is missing
      $this->checkEditRequest($questId);
    } else {
      // unauthorized if user is not an admin
      // redirects if access is missing
      $this->checkCreateRequest();
    }

    $quest = null;
    $questionsWithOptions = [];

    if ($questId != null) {
      $quest = $this->questRepository->getQuestById($questId);
      $questions = $this->questionsRepository->getQuestionsByQuestId($questId);

      foreach ($questions as $question) {
        $options = $this->optionsRepository->getOptionsByQuestionId($question->getQuestionId());
        $questionWithOptions = ['question' => $question, 'options' => $options];
        $questionsWithOptions[] = $questionWithOptions;
      }
    }

    $this->render('layout', ['title' => 'quest add', 'quest' => $quest, 'questionWithOptions' => $questionsWithOptions], 'createQuest');
  }


  private function getSignedUserId(): ?int
  {
    $userSession = $this->sessionService->get('user');
    return $userSession ? $userSession['id'] : null;
  }

  private function getSignedUserRole(): ?string
  {
    $userSession = $this->sessionService->get('user');
    return $userSession ? $userSession['role'] : null;
  }

  private function canUserCreate(): bool
  {
    return $this->getSignedUserRole() === 'admin';
  }

  private function checkEditRequest(int $questId)
  {
    $id = $this->getSignedUserId();
    $quest = $this->questRepository->getQuestById($questId);

    if ($quest === null || $id == null || $quest->getCreatorId() !== $id) {
      $this->redirect('unauthorized');
    }
  }

  private function checkCreateRequest()
  {
    if ($this->getSignedUserRole() !== 'admin') {
      $this->redirect('unauthorized');
    }
  }

  private function checkParticipationRequest(int $questId)
  {
    $id = $this->getSignedUserId();

    if ($id === null) {
      $this->redirect('login');
    } else if ($this->questAuthorizationService->questStatisticsExists($id, $questId)) {
      $this->redirect("unauthorized");
    }
  }

  public function enterQuest($questId)
  {
    $this->checkParticipationRequest($questId);

    $userId = $this->getSignedUserId();

    if ($this->request->isGet()) {
      $this->renderStartQuest($userId, $questId);
    } else {
      $this->startQuest($userId, $questId);
    }
  }

  private function renderStartQuest($userId, $questId)
  {
    $quest = $this->questRepository->getQuestById($questId);
    $wallets = $this->walletRepository->getBlockchainWallets($userId, $quest->getRequiredWallet());

    $this->render('enterQuest', ['title' => 'enter quest', 'wallets' => $wallets, 'message' => '']);
  }

  private function startQuest($userId, $questId)
  {
    $walletSelect = $this->request->post('walletSelect');

    if (!$walletSelect) {
      return $this->redirect('');
    }

    $walletId = $walletSelect;

    if ($walletSelect === 'new') {
      $newWalletAddress = $this->request->post('newWalletAddress');

      if (!$newWalletAddress) {
        return null;
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