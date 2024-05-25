<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Middleware\AuthenticationMiddleware;
use App\Middleware\Authorization\RoleAuthorizationMiddleware;
use App\Middleware\InputValidationMiddleware;
use App\Repository\Roles\RoleRepository;
use App\Routing\Router;
use App\Request\Request;
use App\Services\Authenticate\AuthenticateService;
use App\Services\Authorize\Acl;
use App\Models\Role;
use App\Services\Session\SessionService;
use App\Services\Authenticate\AuthAdapterFactory;
use App\Emitter\Emitter;
use App\Validator\EmailValidationRule;
use App\Validator\MinLengthValidationRule;
use App\Validator\RequiredValidationRule;
use App\Validator\ValidationChain;


$sessionService = new SessionService();
SessionService::start();
$authService = new AuthenticateService($sessionService);
$authAdapterFactory = new AuthAdapterFactory();
$authMiddleware = new AuthenticationMiddleware($authService, $authAdapterFactory, '/showQuests');

$validationChain = new ValidationChain();
$validationChain->addRule('email', new RequiredValidationRule());
$validationChain->addRule('email', new EmailValidationRule());
$validationChain->addRule('password', new RequiredValidationRule());
$validationChain->addRule('password', new MinLengthValidationRule(8));

$loginValidationMiddleware = new InputValidationMiddleware($validationChain);

Router::get('/error/{code}', 'ErrorController@error');

Router::get('/login', 'LoginController@login', [$authMiddleware, $loginValidationMiddleware]);
Router::post('/login', 'LoginController@login', [$authMiddleware]);
Router::get('/logout', 'LoginController@logout', [$authMiddleware]);

Router::get('/register', 'RegisterController@register', [$authMiddleware]);
Router::post('/register', 'RegisterController@register', [$authMiddleware]);

Router::get('/showQuests', 'QuestsController@index');
Router::get('/showCreatedQuests', 'QuestsController@showCreatedQuests', [$authMiddleware]);

$acl = new Acl();

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

Router::post('/createQuest', 'QuestsController@createQuest', [$authMiddleware, $authorizeMiddleware]);
Router::post('/editQuest/{questId}', 'QuestsController@editQuest', [$authMiddleware, $authorizeMiddleware]);

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

