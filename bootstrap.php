<?php

namespace App;

use App\Container\Container;
use App\Database\Database;
use App\Database\DefaultDBConfig;
use App\Database\IDatabase;
use App\Middleware\Authentication\AuthenticationMiddleware;
use App\Middleware\Authorization\RoleAuthorizationMiddleware;
use App\Middleware\LoginValidation\LoginChainFactory;
use App\Middleware\LoginValidation\LoginValidationMiddleware;
use App\Middleware\QuestAuthorization\QuestAuthorizationMiddleware;
use App\Middleware\QuestValidation\QuestValidationChain;
use App\Middleware\QuestValidation\QuestValidationMiddleware;
use App\Middleware\RegisterValidation\RegisterChainFactory;
use App\Middleware\RegisterValidation\RegisterValidationMiddleware;
use App\Models\UserRole;
use App\Repository\IOptionsRepository;
use App\Repository\IQuestionsRepository;
use App\Repository\IQuestRepository;
use App\Repository\IUserRepository;
use App\Repository\IWalletRepository;
use App\Repository\OptionsRepository;
use App\Repository\QuestionsRepository;
use App\Repository\QuestProgress\IQuestProgressRepository;
use App\Repository\QuestProgress\QuestProgressRepository;
use App\Repository\QuestRepository;
use App\Repository\Rating\IRatingRepository;
use App\Repository\Rating\RatingRepository;
use App\Repository\Role\IRoleRepository;
use App\Repository\Role\RoleRepository;
use App\Repository\Similarity\ISimilarityRepository;
use App\Repository\Similarity\SimilarityRepository;
use App\Repository\UserRepository;
use App\Repository\WalletRepository;
use App\Request\IFullRequest;
use App\Services\Authenticate\AuthAdapterFactory;
use App\Services\Authenticate\AuthenticateService;
use App\Services\Authenticate\IAuthAdapterFactory;
use App\Services\Authenticate\IAuthService;
use App\Services\Authorize\Acl;
use App\Services\Authorize\IAcl;
use App\Services\Authorize\Quest\AuthorizationFactory;
use App\Services\Authorize\Quest\QuestAuthorizeService;
use App\Services\Question\IQuestionService;
use App\Services\Question\QuestionService;
use App\Services\QuestProgress\IQuestProgressManager;
use App\Services\QuestProgress\IQuestProgressProvider;
use App\Services\QuestProgress\QuestProgressManager;
use App\Services\QuestProgress\QuestProgressProvider;
use App\Services\Quests\Builder\IQuestBuilderService;
use App\Services\Quests\Builder\QuestBuilder;
use App\Services\Quests\Builder\QuestBuilderService;
use App\Services\Quests\IQuestManager;
use App\Services\Quests\QuestManager;
use App\Services\Quests\QuestProvider;
use App\Services\Rating\IRatingService;
use App\Services\Rating\RatingService;
use App\Services\Recommendation\Data\DataManager;
use App\Services\Recommendation\Data\IDataManager;
use App\Services\Recommendation\IRecommendationService;
use App\Services\Recommendation\Prediction\KnnPredictor;
use App\Services\Recommendation\RecommendationService;
use App\Services\Recommendation\Recommender\IRecommender;
use App\Services\Recommendation\Recommender\Recommender;
use App\Services\Recommendation\Similarity\CosineSimilarity;
use App\Services\Register\DbRegisterStrategy;
use App\Services\Register\IRegisterService;
use App\Services\Register\RegisterService;
use App\Services\Register\StrategyFactory;
use App\Services\Session\ISessionService;
use App\Services\Session\SessionService;
use App\Services\User\IUserService;
use App\Services\User\UserService;
use App\Services\Wallets\IWalletService;
use App\Services\Wallets\WalletService;
use App\View\IViewRenderer;
use App\View\ViewRenderer;
use Exception;

$app = new Container();

// SESSION
$app->set(ISessionService::class, function () {
  $session = new SessionService();
  $session->start();
  return $session;
});

// AUTHENTICATION
$app->set(IAuthService::class, function ($app) {
  return new AuthenticateService($app->get(ISessionService::class));
});

$app->set(IAuthAdapterFactory::class, function ($app) {
  return new AuthAdapterFactory($app->get(IUserRepository::class));
});

$app->set(AuthenticationMiddleware::class, function ($app) {
  return new AuthenticationMiddleware($app->get(IAuthService::class), $app->get(IAuthAdapterFactory::class), '/showQuests');
});

$app->set(IViewRenderer::class, function () {
  return new ViewRenderer('public/views/');
});

$app->set(RegisterValidationMiddleware::class, function () {
  $registerValidationFactory = new RegisterChainFactory();
  return new RegisterValidationMiddleware($registerValidationFactory);
});

$app->set(LoginValidationMiddleware::class, function () {
  $loginValidationFactory = new LoginChainFactory();
  return new LoginValidationMiddleware($loginValidationFactory);
});

$app->set(QuestValidationMiddleware::class, function () {
  $questValidation = new QuestValidationChain();
  return new QuestValidationMiddleware($questValidation);
});

$app->set(RoleAuthorizationMiddleware::class, function ($app) {
  return new RoleAuthorizationMiddleware($app->get(IAcl::class), $app->get(IAuthService::class));
});


$app->set(QuestAuthorizationMiddleware::class, function ($app) {
  $questAuthorizeStrategyFactory = new AuthorizationFactory($app->get(ISessionService::class), $app->get(IAuthService::class), $app->get(IQuestProgressRepository::class), $app->get(IQuestRepository::class));
  $questAuthorizeService = new QuestAuthorizeService($questAuthorizeStrategyFactory);
  return new QuestAuthorizationMiddleware($questAuthorizeService);
});

$app->singleton(IDatabase::class, function () {
  return Database::getInstance(new DefaultDBConfig());
});

$app->set(IUserRepository::class, function ($app) {
  return new UserRepository($app->get(IDatabase::class));
});

$app->set(IQuestRepository::class, function ($app) {
  return new QuestRepository($app->get(IDatabase::class));
});

$app->set(IQuestionsRepository::class, function ($app) {
  return new QuestionsRepository($app->get(IDatabase::class));
});

$app->set(IOptionsRepository::class, function ($app) {
  return new OptionsRepository($app->get(IDatabase::class));
});

$app->set(IWalletRepository::class, function ($app) {
  return new WalletRepository($app->get(IDatabase::class));
});

$app->set(IQuestProgressRepository::class, function ($app) {
  return new QuestProgressRepository($app->get(IDatabase::class));
});

$app->set(IRatingRepository::class, function ($app) {
  return new RatingRepository($app->get(IDatabase::class));
});

$app->set(ISimilarityRepository::class, function ($app) {
  return new SimilarityRepository($app->get(IDatabase::class));
});

$app->set(IRoleRepository::class, function ($app) {
  return new RoleRepository($app->get(IDatabase::class));
});

$app->set(IRegisterService::class, function ($app) {
  $request = $app->get(IFullRequest::class);
  $strategyFactory = new StrategyFactory();
  $strategyFactory->registerStrategy('db', new DbRegisterStrategy($request, $app->get(IUserRepository::class), $app->get(IRoleRepository::class)));

  return new RegisterService($request, $strategyFactory);
});

$app->set(IUserService::class, function ($app) {
  return new UserService($app->get(IUserRepository::class), $app->get(IRoleRepository::class));
});

$app->set(\App\Services\Quests\IQuestProvider::class, function ($app) {
  return new QuestProvider($app->get(IQuestRepository::class), $app->get(IQuestionService::class), $app->get(IWalletRepository::class));
});

$app->set(IQuestManager::class, function ($app) {
  return new QuestManager($app->get(IQuestRepository::class), $app->get(IQuestionService::class));
});

$app->set(IQuestionService::class, function ($app) {
  return new QuestionService($app->get(IQuestionsRepository::class), $app->get(IOptionsRepository::class));
});

$app->set(IQuestProgressProvider::class, function ($app) {
  return new QuestProgressProvider($app->get(ISessionService::class), $app->get(IQuestProgressRepository::class), $app->get(\App\Services\Quests\IQuestProvider::class));
});

$app->set(IQuestProgressManager::class, function ($app) {
  return new QuestProgressManager($app->get(ISessionService::class), $app->get(IQuestProgressRepository::class), $app->get(IQuestionsRepository::class), $app->get(\App\Services\Quests\IQuestProvider::class), $app->get(IQuestManager::class), $app->get(IQuestProgressProvider::class));
});

$app->set(IQuestBuilderService::class, function ($app) {
  $questBuilder = new QuestBuilder();
  return new QuestBuilderService($questBuilder);
});

$app->set(IWalletService::class, function ($app) {
  return new WalletService($app->get(IWalletRepository::class));
});

$app->set(IRatingService::class, function ($app) {
  return new RatingService($app->get(IRatingRepository::class), $app->get(IQuestRepository::class), $app->get(IUserRepository::class));
});

$app->set(IDataManager::class, function ($app) {
  return new DataManager();
});

$app->set(IRecommender::class, function ($app) {
  $recommender = new Recommender($app->get(IDataManager::class));
  $recommender->setSimilarityStrategy(new CosineSimilarity());
  $recommender->setPredictionStrategy(new KnnPredictor($app->get(IDataManager::class)));
  return $recommender;
});

$app->set(IRecommendationService::class, function ($app) {
  return new RecommendationService($app->get(IRatingService::class), $app->get(ISimilarityRepository::class), $app->get(IRecommender::class), $app->get(IDataManager::class));
});


$app->set(IAcl::class, function ($app) {
  $roleRepository = $app->get(IRoleRepository::class);

  try {
    $rolesFromDatabase = $roleRepository->getRoles();
  } catch (Exception $e) {
    die();
  }

  $acl = new Acl();
  foreach ($rolesFromDatabase as $role) {
    $name = $role->getName();

    $acl->addRole($name);
  }


  $acl->allow(UserRole::CREATOR->value, 'QuestViewController', 'showCreatedQuests');
  $acl->allow(UserRole::CREATOR->value, 'QuestViewController', 'questReport');
  $acl->allow(UserRole::ADMIN->value, 'QuestViewController', 'showQuestsToApproval');
  $acl->allow(UserRole::ADMIN->value, 'QuestViewController', 'showApprovedQuests');

  $acl->allow(UserRole::CREATOR->value, 'QuestManagementController', 'createQuest');
  $acl->allow(UserRole::CREATOR->value, 'QuestManagementController', 'showCreateQuest');
  $acl->allow(UserRole::CREATOR->value, 'QuestManagementController', 'showEditQuest');
  $acl->allow(UserRole::CREATOR->value, 'QuestManagementController', 'editQuest');
  // to preview
  $acl->allow(UserRole::ADMIN->value, 'QuestManagementController', 'showEditQuest');
  $acl->allow(UserRole::CREATOR->value, 'UploadController', 'uploadPicture');
  $acl->allow(UserRole::NORMAL->value, 'WalletManagementController', 'showQuestWallets');
  $acl->allow(UserRole::NORMAL->value, 'WalletManagementController', 'addWallet');
  $acl->allow(UserRole::NORMAL->value, 'GameController', 'enterQuest');
  $acl->allow(UserRole::ADMIN->value, 'AdminController', 'refreshRecommendations');
  $acl->allow(UserRole::ADMIN->value, 'AdminController', 'publishQuest');
  $acl->allow(UserRole::ADMIN->value, 'AdminController', 'unpublishQuest');
  $acl->allow(UserRole::ADMIN->value, 'AdminController', 'promoteUser');

  return $acl;
});


return $app;