<?php
require_once __DIR__ . '/vendor/autoload.php';

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
use App\Repository\QuestProgress\QuestProgressRepository;
use App\Repository\QuestRepository;
use App\Repository\Role\RoleRepository;
use App\Routing\Router;
use App\Request\Request;
use App\Services\Authenticate\AuthenticateService;
use App\Services\Authorize\Acl;
use App\Services\Authorize\Quest\AuthorizationFactory;
use App\Services\Authorize\Quest\QuestAuthorizeService;
use App\Services\Session\SessionService;
use App\Services\Authenticate\AuthAdapterFactory;
use App\Emitter\Emitter;

$sessionService = new SessionService();
SessionService::start();

// AUTHENTICATION
$authService = new AuthenticateService($sessionService);
$authAdapterFactory = new AuthAdapterFactory();
$authMiddleware = new AuthenticationMiddleware($authService, $authAdapterFactory, '/showQuests');

// VALIDATION
$registerValidationFactory = new RegisterChainFactory();
$registerValidationMiddleware = new RegisterValidationMiddleware($registerValidationFactory);
$loginValidationFactory = new LoginChainFactory();
$loginValidationMiddleware = new LoginValidationMiddleware($loginValidationFactory);
$questValidation = new QuestValidationChain();
$questValidationMiddleware = new QuestValidationMiddleware($questValidation);

// AUTHORIZATION
$questAuthorizeStrategyFactory = new AuthorizationFactory($sessionService, $authService, new QuestProgressRepository(), new QuestRepository());
$questAuthorizeService = new QuestAuthorizeService($questAuthorizeStrategyFactory);
$questAuthorizeMiddleware = new QuestAuthorizationMiddleware($questAuthorizeService);

// ACL

$roleRepository = new RoleRepository();
$rolesFromDatabase = $roleRepository->getRoles();
$acl = new Acl();
foreach ($rolesFromDatabase as $role) {
  $name = $role->getName();

  $acl->addRole($name);
}


$acl->allow((string) UserRole::CREATOR->value, 'QuestsController', 'showCreatedQuests');
$acl->allow((string) UserRole::CREATOR->value, 'QuestsController', 'createQuest');
$acl->allow((string) UserRole::CREATOR->value, 'QuestsController', 'showCreateQuest');
$acl->allow((string) UserRole::CREATOR->value, 'QuestsController', 'showEditQuest');
$acl->allow((string) UserRole::CREATOR->value, 'QuestsController', 'editQuest');
$acl->allow((string) UserRole::CREATOR->value, 'QuestsController', 'uploadQuestPicture');
$acl->allow((string) UserRole::CREATOR->value, 'QuestsController', 'reportQuest');

$acl->allow((string) UserRole::NORMAL->value, 'QuestsController', 'showQuestWallets');
$acl->allow((string) UserRole::NORMAL->value, 'QuestsController', 'enterQuest');
$acl->allow((string) UserRole::NORMAL->value, 'QuestsController', 'addWallet');

$acl->allow((string) UserRole::ADMIN->value, 'QuestsController', 'refreshRecommendations');
$acl->allow((string) UserRole::ADMIN->value, 'QuestsController', 'showQuestsToApproval');
$acl->allow((string) UserRole::ADMIN->value, 'QuestsController', 'showApprovedQuests');
$acl->allow((string) UserRole::ADMIN->value, 'QuestsController', 'publishQuest');
$acl->allow((string) UserRole::ADMIN->value, 'QuestsController', 'unpublishQuest');
$acl->allow((string) UserRole::ADMIN->value, 'QuestsController', 'showEditQuest');

$roleAuthorizationMiddleware = new RoleAuthorizationMiddleware($acl, $authService);

// GENERAL ROUTES
Router::get('/error/{code}', 'ErrorController@error');
Router::get('/', 'QuestsController@showQuests', [$authMiddleware, $questAuthorizeMiddleware, $questAuthorizeMiddleware]);

// PROFILE ROUTES
Router::post('/changePassword', 'ProfileController@changePassword', [$authMiddleware]);
Router::get('/dashboard', 'ProfileController@showProfile', [$authMiddleware]);

// AUTHENTICATION ROUTES
Router::get('/login', 'LoginController@login', [$authMiddleware]);
Router::get('/logout', 'LoginController@logout', [$authMiddleware]);
Router::get('/register', 'RegisterController@register', [$authMiddleware]);
Router::post('/login', 'LoginController@login', [$loginValidationMiddleware, $authMiddleware]);
Router::post('/register', 'RegisterController@register', [$authMiddleware, $registerValidationMiddleware]);

// CREATOR ROUTES`
Router::get('/showCreatedQuests', 'QuestsController@showCreatedQuests', [$authMiddleware, $roleAuthorizationMiddleware]);
Router::get('/showCreateQuest', 'QuestsController@showCreateQuest', [$authMiddleware, $roleAuthorizationMiddleware]);
Router::get('/showEditQuest/{questId}', 'QuestsController@showEditQuest', [$authMiddleware, $roleAuthorizationMiddleware]);
Router::post('/createQuest', 'QuestsController@createQuest', [$authMiddleware, $roleAuthorizationMiddleware, $questValidationMiddleware, $questAuthorizeMiddleware]);
Router::post('/editQuest/{questId}', 'QuestsController@editQuest', [$authMiddleware, $roleAuthorizationMiddleware, $questValidationMiddleware, $questAuthorizeMiddleware]);
Router::post('/uploadQuestPicture', 'QuestsController@uploadQuestPicture', [$authMiddleware, $roleAuthorizationMiddleware]);
Router::get('/reportQuest/{questId}', 'QuestsController@reportQuest', [$authMiddleware, $roleAuthorizationMiddleware]);

// NORMAL USER ROUTES
Router::get('/showQuests', 'QuestsController@showQuests', [$authMiddleware, $roleAuthorizationMiddleware, $questAuthorizeMiddleware]);
Router::get('/showTopRatedQuests', 'QuestsController@showTopRatedQuests', [$authMiddleware, $roleAuthorizationMiddleware, $questAuthorizeMiddleware]);
Router::get('/showRecommendedQuests', 'QuestsController@showRecommendedQuests', [$authMiddleware, $roleAuthorizationMiddleware, $questAuthorizeMiddleware]);
Router::get('/showQuestWallets/{questId}', 'QuestsController@showQuestWallets', [$authMiddleware, $questAuthorizeMiddleware, $questAuthorizeMiddleware]);
Router::post('/addWallet/{blockchain}', 'QuestsController@addWallet', [$authMiddleware, $roleAuthorizationMiddleware, $questAuthorizeMiddleware]);
Router::post('/enterQuest/{questId}', 'GameController@enterQuest', [$authMiddleware, $roleAuthorizationMiddleware, $questAuthorizeMiddleware]);
Router::get('/play', 'GameController@play', [$authMiddleware, $roleAuthorizationMiddleware, $questAuthorizeMiddleware]);
Router::post('/answer/{questionId}', 'GameController@answer', [$authMiddleware, $roleAuthorizationMiddleware, $questAuthorizeMiddleware]);
Router::post('/rating', 'GameController@rating', [$authMiddleware, $roleAuthorizationMiddleware, $questAuthorizeMiddleware]);
Router::post('/abandonQuest', 'GameController@abandonQuest', [$authMiddleware]);
Router::get('/endQuest', 'GameController@reset', [$authMiddleware]);

// Admin routes
Router::get('/refreshRecommendations', 'QuestsController@refreshRecommendations', [$authMiddleware, $roleAuthorizationMiddleware]);
Router::get('/showQuestsToApproval', 'QuestsController@showQuestsToApproval', [$authMiddleware, $roleAuthorizationMiddleware]);
Router::get('/showApprovedQuests', 'QuestsController@showApprovedQuests', [$authMiddleware, $roleAuthorizationMiddleware]);
Router::post('/publishQuest/{questId}', 'QuestsController@publishQuest', [$authMiddleware, $roleAuthorizationMiddleware]);
Router::post('/unpublishQuest/{questId}', 'QuestsController@unpublishQuest', [$authMiddleware, $roleAuthorizationMiddleware]);

$request = new Request($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
$response = Router::dispatch($request);
$emitter = new Emitter();
$emitter->emit($response);


// Router::get('startQuest', 'QuestsController');
// // PROFILE
// Router::get('dashboard', 'QuestsController');

// // GAMEPLAY
// Router::get('gameplay', 'GameController');
// Router::get('processUserResponse', 'GameController');
// Router::get('nextQuestion', 'GameController');

