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
use App\Repository\QuestProgress\QuestProgressRepository;
use App\Repository\QuestRepository;
use App\Routing\Router;
use App\Request\Request;
use App\Services\Authenticate\AuthenticateService;
use App\Services\Authorize\Acl;
use App\Models\Role;
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
$acl = new Acl();
$admin = new Role('admin');
$user = new Role('normal');
$guest = new Role('guest');
$creator = new Role('creator');

$acl->addRole($admin);
$acl->addRole($user);
$acl->addRole($guest);
$acl->addRole($creator);

$acl->allow($creator, 'QuestsController', 'createQuest');
$acl->allow($creator, 'QuestsController', 'editQuest');

$roleAuthorizationMiddleware = new RoleAuthorizationMiddleware($acl, $authService);

// ROUTES
Router::get('/error/{code}', 'ErrorController@error');
Router::get('/', 'QuestsController@index', [$authMiddleware]);
Router::get('/login', 'LoginController@login', [$authMiddleware]);
Router::post('/login', 'LoginController@login', [$loginValidationMiddleware, $authMiddleware]);
Router::get('/logout', 'LoginController@logout', [$authMiddleware]);
Router::get('/register', 'RegisterController@register', [$authMiddleware]);
Router::post('/register', 'RegisterController@register', [$authMiddleware, $registerValidationMiddleware]);
Router::get('/showQuests', 'QuestsController@index', [$authMiddleware]);
Router::get('/showCreatedQuests', 'QuestsController@showCreatedQuests', [$authMiddleware]);
Router::get('/createQuest', 'QuestsController@createQuest', [$authMiddleware, $roleAuthorizationMiddleware]);
Router::get('/editQuest/{questId}', 'QuestsController@editQuest', [$authMiddleware, $roleAuthorizationMiddleware]);
Router::post('/createQuest', 'QuestsController@createQuest', [$authMiddleware, $roleAuthorizationMiddleware, $questValidationMiddleware]);
Router::post('/editQuest/{questId}', 'QuestsController@editQuest', [$authMiddleware, $roleAuthorizationMiddleware, $questValidationMiddleware]);

$request = new Request($_SERVER, $_GET, $_POST);
$response = Router::dispatch($request);
$emitter = new Emitter();
$emitter->emit($response);


// Router::get('showQuestWallets', 'QuestsController');
// Router::get('startQuest', 'QuestsController');
// // PROFILE
// Router::get('dashboard', 'QuestsController');

// // GAMEPLAY
// Router::get('gameplay', 'GameController');
// Router::get('processUserResponse', 'GameController');
// Router::get('nextQuestion', 'GameController');

