<?php

namespace App\Controllers;

use App\Controllers\AppController;
use App\Controllers\Interfaces\IQuestsController;
use App\Middleware\JsonResponse;
use App\Middleware\RedirectResponse;
use App\Models\IQuest;
use App\Models\Wallet;
use App\Repository\IUserRepository;
use App\Repository\UserRepository;
use App\Request\IFullRequest;
use App\Request\IRequest;
use App\Middleware\IResponse;
use App\Services\Authenticate\AuthenticateService;
use App\Services\Authenticate\IAuthService;
use App\Services\QuestProgress\IQuestProgressService;
use App\Services\QuestProgress\QuestProgressService;
use App\Services\Quests\Builder\IQuestBuilderService;
use App\Services\Quests\Builder\QuestBuilder;
use App\Services\Quests\Builder\QuestBuilderService;
use App\Services\Quests\IQuestService;
use App\Services\Quests\QuestService;
use App\Services\Rating\RatingService;
use App\Services\Recommendation\IRecommendationService;
use App\Services\Recommendation\RecommendationService;
use App\Services\Wallets\IWalletService;
use App\Services\Wallets\WalletService;

class QuestsController extends AppController implements IQuestsController
{
  const MAX_FILE_SIZE = 1024 * 1024;
  const SUPPORTED_TYPES = ['image/png', 'image/jpeg'];
  const UPLOAD_DIRECTORY = '/../public/uploads/';
  private IQuestService $questService;
  private IAuthService $authService;
  private IQuestBuilderService $questBuilderService;
  private IWalletService $walletService;
  private IQuestProgressService $questProgressService;
  private IUserRepository $userRepository;
  private IRecommendationService $recommendationService;

  public function __construct(IFullRequest $request, IQuestService $questService = null, IAuthService $authService = null, IQuestBuilderService $questBuilderService = null, IWalletService $walletService = null, IQuestProgressService $questProgressService = null, IUserRepository $userRepository = null, IRecommendationService $recommendationService = null)
  {
    parent::__construct($request);
    $this->questService = $questService ?: new QuestService();
    $this->authService = $authService ?: new AuthenticateService($this->sessionService);
    $this->questBuilderService = $questBuilderService ?: new QuestBuilderService(new QuestBuilder());
    $this->walletService = $walletService ?: new WalletService();
    $this->questProgressService = $questProgressService ?: new QuestProgressService($this->sessionService);
    $this->userRepository = $userRepository ?: new UserRepository();
    $this->recommendationService = $recommendationService ?: new RecommendationService();
  }

  /*
      User actions
  */
  public function getIndex(IRequest $request): IResponse
  {
    return $this->getShowQuests($request);
  }

  // shows all quests which are approved and can be played
  public function getShowQuests(IRequest $request): IResponse
  {
    $id = $this->authService->getIdentity()->getId();
    $quests = $this->questService->getQuestsToPlay();

    $quests = array_filter($quests, function ($quest) use ($id) {
      return !$this->questProgressService->isQuestPlayed($id, $quest->getQuestID());
    });

    return $this->render('layout', ['title' => 'quest list', 'quests' => $quests], 'quests');
  }

  public function getShowTopRatedQuests(IRequest $request): IResponse
  {
    $quests = $this->questService->getTopRatedQuests();

    return new JsonResponse(['quests' => $quests], 200);
  }

  public function getShowRecommendedQuests(IRequest $request): IResponse
  {
    $userId = $this->authService->getIdentity()->getId();
    $questsIds = $this->recommendationService->getRecommendations($userId);
    $quests = $this->questService->getQuests($questsIds);
    return new JsonResponse(['quests' => $quests], 200);
  }

  /*
    Creator actions
  */
  private function renderEditAndCreateView(IQuest $quest = null): IResponse
  {
    return $this->render('layout', ['title' => 'quest add', 'quest' => $quest], 'createQuest');
  }


  // returns create quest view
  public function getCreateQuest(IRequest $request): IResponse
  {
    return $this->renderEditAndCreateView();
  }

  // returns edit quest view
  public function getEditQuest(IRequest $request, int $questId): IResponse
  {
    $quest = $this->questService->getQuestWithQuestions($questId);

    if (!$quest) {
      return new RedirectResponse('/error/404', 0);
    }

    if ($quest->getIsApproved()) {
      return new RedirectResponse('/error/401');
    }

    return $this->renderEditAndCreateView($quest);
  }

  // show created quests list which are not approved yet, but can be edited by creator
  public function getShowCreatedQuests(IRequest $request): IResponse
  {
    $quests = $this->questService->getCreatorQuests($this->authService->getIdentity());

    return $this->render('layout', ['title' => 'created quests', 'quests' => $quests], 'createdQuests');
  }

  public function postCreateQuest(IRequest $request): IResponse
  {
    $formData = $this->request->getBody();
    $parsedData = json_decode($formData, true);
    $creatorId = $this->authService->getIdentity()->getId();
    $parsedData['creatorId'] = $creatorId;
    $quest = $this->questBuilderService->buildQuest($parsedData);
    $questResult = $this->questService->createQuest($quest);

    if (!$questResult->isSuccess()) {
      return new JsonResponse(['errors' => $questResult->getMessages()]);
    } else {
      return new JsonResponse(['redirectUrl' => '/showCreatedQuests']);
    }
  }

  public function postEditQuest(IRequest $request, int $questId): IResponse
  {
    $formData = $this->request->getBody();
    $parsedData = json_decode($formData, true);
    $parsedData['questId'] = $questId;
    $quest = $this->questBuilderService->buildQuest($parsedData);
    $questResult = $this->questService->editQuest($quest);

    if (!$questResult->isSuccess()) {
      return new JsonResponse(['errors' => $questResult->getMessages()]);
    } else {
      return new JsonResponse(['redirectUrl' => '/showCreatedQuests']);
    }
  }

  private function validateQuestFile($fileData): array
  {
    $errors = [];


    if ($fileData['size'] > self::MAX_FILE_SIZE) {
      $errors[] = 'file is too big';
    }

    if (!in_array($fileData['type'], self::SUPPORTED_TYPES)) {
      $errors[] = 'file type is not supported';
    }

    return $errors;
  }

  private function generateFileName($filePath): string
  {
    $imageFileType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $uniqueName = uniqid() . '.' . $imageFileType;
    $targetFile = $filePath . $uniqueName;

    $counter = 1;
    while (file_exists($targetFile)) {
      $baseName = pathinfo($filePath, PATHINFO_FILENAME);
      $extension = pathinfo($filePath, PATHINFO_EXTENSION);
      $targetFile = $filePath . $baseName . "_$counter" . $extension;
      $counter++;
    }

    return $targetFile;
  }

  public function postUploadQuestPicture(IRequest $request): IResponse
  {
    $fileData = $this->request->getUploadedFiles()['file'];
    $errors = $this->validateQuestFile($fileData);

    if ($errors) {
      return new JsonResponse(['errors' => $errors]);
    }

    if ($fileData && is_uploaded_file($fileData['tmp_name'])) {
      $newFileName = $this->generateFileName($fileData['name']);
      move_uploaded_file(
        $fileData['tmp_name'],
        dirname(__DIR__) . self::UPLOAD_DIRECTORY . $newFileName
      );
      return new JsonResponse(['name' => $newFileName]);
    }

    return new JsonResponse(['errors' => ['file not uploaded']]);
  }


  /*
      Admin actions
  */

  // shows list of quests which are not approved yet, but can be approved by admin
  public function getShowQuestsToApproval(IRequest $request): IResponse
  {
    $quests = $this->questService->getQuestsToApproval();

    return $this->render('layout', ['title' => 'quests to approval', 'quests' => $quests], 'questsToApproval');
  }

  // publishes/approves quest
  public function postPublish(IRequest $request, int $questId): IResponse
  {
    $this->questService->publishQuest($questId);

    return new JsonResponse(['message' => 'quest published']);
  }

  public function getRefreshRecommendations(IRequest $request): IResponse
  {
    $this->recommendationService->refreshRecommendations();
    return new JsonResponse(['message' => 'recommendations refreshed']);
  }

  public function getShowQuestWallets(IRequest $request, int $questId): IResponse
  {
    $identity = $this->authService->getIdentity();
    $blockchain = $this->questService->getQuestBlockchain($questId);
    $wallets = $this->walletService->getBlockchainWallets($identity, $blockchain);

    return $this->render('showWallets', ['title' => 'enter quest', 'questId' => $questId, 'wallets' => $wallets, 'blockchain' => $blockchain]);
  }


  public function postAddWallet(IRequest $request, string $blockchain): IResponse
  {
    $userId = $this->authService->getIdentity()->getId();
    $walletAddress = $this->request->getParsedBodyParam('walletAddress');
    $wallet = new Wallet(0, $userId, $blockchain, $walletAddress, date('Y-m-d'), date('Y-m-d'));
    $walletId = $this->walletService->createWallet($wallet);

    return new JsonResponse(['walletId' => $walletId, 'walletAddress' => $walletAddress]);
  }

  public function getDashboard(IRequest $request): IResponse
  {
    $userId = $this->authService->getIdentity()->getId();
    $user = $this->userRepository->getUserById($userId);
    $joinDate = \DateTime::createFromFormat('Y-m-d', $user->getJoinDate())->format('F Y');

    $stats = $this->questProgressService->getUserQuests($userId);


    // somehow get user points
    // there is also list of quests user has participated in
    return $this->render('layout', ['title' => 'dashboard', 'username' => $user->getName(), 'joinDate' => $joinDate, 'points' => sizeof($stats)], 'dashboard');
  }
}