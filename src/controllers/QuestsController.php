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

  public function __construct()
  {
    parent::__construct();
    $this->questRepository = new QuestRepository();
    $this->walletRepository = new WalletRepository();
    $this->questAuthorizationService = new QuestAuthorizationService();
  }

  public function quests()
  {
    $quests = $this->questRepository->getQuests();
    $this->render('quests', ['title' => 'quest list', 'quests' => $quests]);
  }

  public function enterQuest($questId)
  {
    session_start();

    $userId = $_SESSION['user_id'];

    if (!isset($userId)) {
      return $this->redirectToLogin();
    }

    if (!$this->questAuthorizationService->isUserAuthorized($userId, $questId)) {
      return $this->redirectToUnauthorized();
    }

    if (!$this->isPost()) {
      return $this->renderEnterQuestPage($userId, $questId);
    } else {
      return $this->handlePostEnterQuestRequest($userId, $questId);
    }
  }

  private function renderEnterQuestPage($userId, $questId)
  {
    $quest = $this->questRepository->getQuestById($questId);
    $wallets = $this->walletRepository->getBlockchainWallets($userId, $quest->getRequiredWallet());
    return $this->render('enterQuest', ['title' => 'enter quest', 'wallets' => $wallets]);
  }

  private function handlePostEnterQuestRequest($userId, $questId)
  {
    $walletSelect = $_POST['walletSelect'] ?? null;

    if (!$walletSelect) {
      return $this->redirectToQuests();
    }

    if ($walletSelect === 'new') {
      return $this->handleNewWallet($userId, $questId);
    } else {
      $_SESSION['wallet_id'] = $walletSelect;
      return $this->redirectToGameplay($questId);
    }
  }

  private function handleNewWallet($userId, $questId)
  {
    $newWalletAddress = $_POST['newWalletAddress'] ?? null;
    if (!$newWalletAddress) {
      return $this->redirectToQuests();
    }

    $quest = $this->questRepository->getQuestById($questId);
    $wallet = new Wallet(0, $userId, $quest->getRequiredWallet(), $newWalletAddress, date('Y-m-d'), date('Y-m-d'));
    $walletId = $this->walletRepository->addWallet($wallet);

    $_SESSION['wallet_id'] = $walletId;

    return $this->redirectToGameplay($questId);
  }

  private function redirectToGameplay($questId)
  {

    $_SESSION['questId'] = $questId;
    $url = "http://$_SERVER[HTTP_HOST]";
    header("Location: {$url}/gameplay/{$questId}");
  }

  private function redirectToUnauthorized()
  {
    $url = "http://$_SERVER[HTTP_HOST]";
    header("Location: {$url}/unauthorized");
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
}