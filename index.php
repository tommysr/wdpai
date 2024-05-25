<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Middleware\Authentication\AuthenticationMiddleware;
use App\Middleware\Authorization\RoleAuthorizationMiddleware;
use App\Middleware\LoginValidation\LoginChainFactory;
use App\Middleware\LoginValidation\LoginValidationMiddleware;
use App\Middleware\QuestValidation\QuestValidationChain;
use App\Middleware\QuestValidation\QuestValidationMiddleware;
use App\Middleware\RegisterValidation\RegisterChainFactory;
use App\Middleware\RegisterValidation\RegisterValidationMiddleware;
use App\Routing\Router;
use App\Request\Request;
use App\Services\Authenticate\AuthenticateService;
use App\Services\Authorize\Acl;
use App\Models\Role;
use App\Services\Session\SessionService;
use App\Services\Authenticate\AuthAdapterFactory;
use App\Emitter\Emitter;

$sessionService = new SessionService();
SessionService::start();

$authService = new AuthenticateService($sessionService);
$authAdapterFactory = new AuthAdapterFactory();
$authMiddleware = new AuthenticationMiddleware($authService, $authAdapterFactory, '/showQuests');

// some generic 
Router::get('/error/{code}', 'ErrorController@error');

// this looks good, checked
Router::get('/login', 'LoginController@login', [$authMiddleware]);
$loginValidationFactory = new LoginChainFactory();
Router::post('/login', 'LoginController@login', [new LoginValidationMiddleware($loginValidationFactory), $authMiddleware]);
Router::get('/logout', 'LoginController@logout', [$authMiddleware]);

// same, checked
Router::get('/register', 'RegisterController@register', [$authMiddleware]);
$registerValidationFactory = new RegisterChainFactory();
Router::post('/register', 'RegisterController@register', [$authMiddleware, new RegisterValidationMiddleware($registerValidationFactory)]);

Router::get('/showQuests', 'QuestsController@index', [$authMiddleware]);
Router::get('/showCreatedQuests', 'QuestsController@showCreatedQuests', [$authMiddleware]);

$acl = new Acl();

// maybe get roles from db
$admin = new Role('admin');
$user = new Role('user');
$guest = new Role('guest');
$creator = new Role('creator');

$acl->addRole($admin);
$acl->addRole($user);
$acl->addRole($guest);
$acl->addRole($creator);

$acl->allow($creator, 'QuestsController', 'createQuest');
$authorizeMiddleware = new RoleAuthorizationMiddleware($acl, $authService);


Router::get('/createQuest', 'QuestsController@createQuest', [$authMiddleware, $authorizeMiddleware]);
Router::get('/editQuest/{questId}', 'QuestsController@editQuest', [$authMiddleware, $authorizeMiddleware]);


$questValidation = new QuestValidationChain();
Router::post('/createQuest', 'QuestsController@createQuest', [$authMiddleware, $authorizeMiddleware, new QuestValidationMiddleware($questValidation)]);
Router::post('/editQuest/{questId}', 'QuestsController@editQuest', [$authMiddleware, $authorizeMiddleware, new QuestValidationMiddleware($questValidation)]);

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

