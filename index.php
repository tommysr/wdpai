<?php
require_once __DIR__ . '/vendor/autoload.php';

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
use App\Middleware\RedirectResponse;
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
use App\Routing\Router;
use App\Request\Request;
use App\Services\Authenticate\AuthenticateService;
use App\Services\Authenticate\IAuthAdapterFactory;
use App\Services\Authenticate\IAuthService;
use App\Services\Authorize\Acl;
use App\Services\Authorize\IAcl;
use App\Services\Authorize\Quest\AuthorizationFactory;
use App\Services\Authorize\Quest\QuestAuthorizeService;
use App\Services\QuestProgress\IQuestProgressService;
use App\Services\QuestProgress\QuestProgressService;
use App\Services\Quests\Builder\IQuestBuilder;
use App\Services\Quests\Builder\IQuestBuilderService;
use App\Services\Quests\Builder\QuestBuilder;
use App\Services\Quests\Builder\QuestBuilderService;
use App\Services\Quests\IQuestService;
use App\Services\Quests\QuestService;
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
use App\Services\Register\IRegisterStrategyFactory;
use App\Services\Register\RegisterService;
use App\Services\Register\StrategyFactory;
use App\Services\Session\ISessionService;
use App\Services\Session\SessionService;
use App\Services\Authenticate\AuthAdapterFactory;
use App\Emitter\Emitter;
use App\Container\Container;
use App\Services\User\IUserService;
use App\Services\User\UserService;
use App\Services\Wallets\IWalletService;
use App\Services\Wallets\WalletService;
use App\View\IViewRenderer;
use App\View\ViewRenderer;

$time_start = microtime(true);

$app = new Container();

// SESSION
$app->set(ISessionService::class, function () {
  SessionService::start();
  return new SessionService();
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
  $strategyFactory = new StrategyFactory($request);
  $strategyFactory->registerStrategy('db', new DbRegisterStrategy($request, $app->get(IUserRepository::class), $app->get(IRoleRepository::class)));

  return new RegisterService($request, $strategyFactory);
});

$app->set(IUserService::class, function ($app) {
  return new UserService($app->get(IUserRepository::class), $app->get(IRoleRepository::class));
});

$app->set(IQuestService::class, function ($app) {
  return new QuestService($app->get(IQuestRepository::class), $app->get(IQuestionsRepository::class), $app->get(IOptionsRepository::class), $app->get(IWalletRepository::class));
});

$app->set(IQuestProgressService::class, function ($app) {
  return new QuestProgressService($app->get(ISessionService::class), $app->get(IQuestProgressRepository::class), $app->get(IQuestionsRepository::class), $app->get(IQuestService::class), $app->get(IOptionsRepository::class), $app->get(IWalletRepository::class));
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
  return new DataManager([], []);
});

$app->set(IRecommender::class, function ($app) {
  return new Recommender($app->get(IDataManager::class));
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


$r = new Router($app);

// ERROR ROUTES
$r->get('/error/{code}', 'ErrorController@error');

// QUEST DATA VIEW / FETCH ROUTES
$r->get('/', 'QuestViewController@showQuests', [AuthenticationMiddleware::class, QuestAuthorizationMiddleware::class]);
$r->get('/showCreatedQuests', 'QuestViewController@showCreatedQuests', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class]);
$r->get('/showQuestsToApproval', 'QuestViewController@showQuestsToApproval', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class]);
$r->get('/showApprovedQuests', 'QuestViewController@showApprovedQuests', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class]);
$r->get('/reportQuest/{questId}', 'QuestViewController@questReport', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class]);
$r->get('/showQuests', 'QuestViewController@showQuests', [AuthenticationMiddleware::class, QuestAuthorizationMiddleware::class]);
$r->get('/showTopRatedQuests', 'QuestViewController@showTopRatedQuests', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class, QuestAuthorizationMiddleware::class]);
$r->get('/showRecommendedQuests', 'QuestViewController@showRecommendedQuests', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class, QuestAuthorizationMiddleware::class]);

// PROFILE ROUTES
$r->post('/changePassword', 'ProfileController@changePassword', [AuthenticationMiddleware::class]);
$r->get('/dashboard', 'ProfileController@showProfile', [AuthenticationMiddleware::class]);

// AUTHENTICATION ROUTES
$r->get('/login', 'LoginController@login', [AuthenticationMiddleware::class]);
$r->get('/logout', 'LoginController@logout', [AuthenticationMiddleware::class]);
$r->post('/login', 'LoginController@login', [LoginValidationMiddleware::class, AuthenticationMiddleware::class]);

// REGISTRATION ROUTES
$r->get('/register', 'RegisterController@register', [AuthenticationMiddleware::class]);
$r->post('/register', 'RegisterController@register', [AuthenticationMiddleware::class, RegisterValidationMiddleware::class]);

// QUEST MANAGEMENT ROUTES - CREATOR
$r->get('/showCreateQuest', 'QuestManagementController@showCreateQuest', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class]);
$r->get('/showEditQuest/{questId}', 'QuestManagementController@showEditQuest', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class]);
$r->post('/createQuest', 'QuestManagementController@createQuest', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class, QuestValidationMiddleware::class, QuestAuthorizationMiddleware::class]);
$r->post('/editQuest/{questId}', 'QuestManagementController@editQuest', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class, QuestValidationMiddleware::class, QuestAuthorizationMiddleware::class]);

// WALLET MANAGEMENT ROUTES
$r->get('/showQuestWallets/{questId}', 'WalletManagementController@showQuestWallets', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class, QuestAuthorizationMiddleware::class]);
$r->post('/addWallet/{blockchain}', 'WalletManagementController@addWallet', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class, QuestAuthorizationMiddleware::class]);

// FILE UPLOAD FOR CREATORS
$r->post('/uploadPicture', 'UploadController@uploadPicture', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class]);

// ADMIN CONTROLLER ROUTES
$r->get('/refreshRecommendations', 'AdminController@refreshRecommendations', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class]);
$r->post('/publishQuest/{questId}', 'AdminController@publishQuest', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class]);
$r->post('/unpublishQuest/{questId}', 'AdminController@unpublishQuest', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class]);
$r->get('/promoteToCreator/{userName}', 'AdminController@promoteUser', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class]);

// GAME ROUTES
$r->post('/enterQuest/{questId}', 'GameController@enterQuest', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class, QuestAuthorizationMiddleware::class]);
$r->get('/play', 'GameController@play', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class, QuestAuthorizationMiddleware::class]);
$r->post('/answer/{questionId}', 'GameController@answer', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class, QuestAuthorizationMiddleware::class]);
$r->post('/rating', 'GameController@rating', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class, QuestAuthorizationMiddleware::class]);
$r->post('/abandonQuest', 'GameController@abandonQuest', [AuthenticationMiddleware::class]);
$r->get('/endQuest', 'GameController@reset', [AuthenticationMiddleware::class]);

$request = new Request($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
$emitter = new Emitter();

try {
  $response = $r->dispatch($request);

  $emitter->emit($response);
} catch (Exception $e) {
  error_log($e->getMessage());
  $emitter->emit(new RedirectResponse('/error/500', ['internal server error. contact with the administrator']));
}

