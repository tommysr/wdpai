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


$acl->allow((string) UserRole::CREATOR->value, 'QuestsController', 'createQuest');
$acl->allow((string) UserRole::CREATOR->value, 'QuestsController', 'editQuest');
$acl->allow((string) UserRole::NORMAL->value, 'QuestsController', 'showQuestWallets');
$acl->allow((string) UserRole::NORMAL->value, 'QuestsController', 'enterQuest');
$acl->allow((string) UserRole::NORMAL->value, 'QuestsController', 'addWallet');

$roleAuthorizationMiddleware = new RoleAuthorizationMiddleware($acl, $authService);

// GENERAL ROUTES
Router::get('/error/{code}', 'ErrorController@error');
Router::get('/', 'QuestsController@showQuests', [$authMiddleware, $questAuthorizeMiddleware, $questAuthorizeMiddleware]);

// AUTHENTICATION ROUTES
Router::get('/login', 'LoginController@login', [$authMiddleware]);
Router::get('/logout', 'LoginController@logout', [$authMiddleware]);
Router::get('/register', 'RegisterController@register', [$authMiddleware]);
Router::post('/login', 'LoginController@login', [$loginValidationMiddleware, $authMiddleware]);
Router::post('/register', 'RegisterController@register', [$authMiddleware, $registerValidationMiddleware]);

// CREATOR ROUTES
Router::get('/showCreatedQuests', 'QuestsController@showCreatedQuests', [$authMiddleware, $roleAuthorizationMiddleware]);
Router::get('/createQuest', 'QuestsController@createQuest', [$authMiddleware, $roleAuthorizationMiddleware]);
Router::get('/editQuest/{questId}', 'QuestsController@editQuest', [$authMiddleware, $roleAuthorizationMiddleware]);
Router::post('/createQuest', 'QuestsController@createQuest', [$authMiddleware, $roleAuthorizationMiddleware, $questValidationMiddleware]);
Router::post('/editQuest/{questId}', 'QuestsController@editQuest', [$authMiddleware, $roleAuthorizationMiddleware, $questValidationMiddleware]);

// NORMAL USER ROUTES
Router::get('/showQuests', 'QuestsController@showQuests', [$authMiddleware, $roleAuthorizationMiddleware, $questAuthorizeMiddleware]);
Router::get('/showQuestWallets/{questId}', 'QuestsController@showQuestWallets', [$authMiddleware, $questAuthorizeMiddleware, $questAuthorizeMiddleware]);
Router::post('/addWallet/{blockchain}', 'QuestsController@addWallet', [$authMiddleware, $roleAuthorizationMiddleware, $questAuthorizeMiddleware]);
Router::post('/enterQuest/{questId}', 'QuestsController@enterQuest', [$authMiddleware, $roleAuthorizationMiddleware, $questAuthorizeMiddleware]);
Router::get('/play/{questId}', 'GameController@play', [$authMiddleware, $roleAuthorizationMiddleware, $questAuthorizeMiddleware]); 

$request = new Request($_SERVER, $_GET, $_POST);
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

