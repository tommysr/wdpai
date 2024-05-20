<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Middleware\AuthenticationMiddleware;
use App\Routing\Router;
use App\Request\Request;
use App\Services\Authenticate\AuthenticateService;
use App\Services\Session\SessionService;
use App\Services\Authenticate\AuthAdapterFactory;

// // require_once 'src/services/AuthorizationService.php';

// $path = trim($_SERVER['REQUEST_URI'], '/');
// $path = parse_url($path, PHP_URL_PATH);


// // QUESTS
// Router::get('', 'QuestsController');
// Router::get('quests', 'QuestsController');
// Router::get('showQuestWallets', 'QuestsController');
// Router::get('startQuest', 'QuestsController');


// // QUESTS MANAGEMENT
// Router::get('createQuest', 'QuestsController');
// Router::get('editQuest', 'QuestsController');
// Router::get('createdQuests', 'QuestsController');

// // PROFILE
// Router::get('dashboard', 'QuestsController');


// // AUTH
// Router::get('login', 'AuthController');
// Router::get('logout', 'AuthController');
// Router::get('register', 'AuthController');


// // GAMEPLAY
// Router::get('gameplay', 'GameController');
// Router::get('processUserResponse', 'GameController');
// Router::get('nextQuestion', 'GameController');

// $user_authorization_service = new RoleAuthorizationService(new SessionService());
// $user_middleware = new RoleAuthorizationMiddleware($user_authorization_service, Role::USER);
// $middleware->setNext(new QuestAuthorizationMiddleware(new QuestAuthorizeService(new QuestStatisticsRepository()), QuestRequest::PLAY));

// $userLoggedInMiddleware = new RoleAuthorizationMiddleware(new UserAuthorizationService(new SessionService()), Role::USER);

// Router::get('login', 'AuthController');
// Router::get('logout', 'AuthController');
// Router::get('register', 'AuthController');


$sessionService = new SessionService();
$authService = new AuthenticateService($sessionService);
$authAdapterFactory = new AuthAdapterFactory();
$authMiddleware = new AuthenticationMiddleware($authService, $authAdapterFactory);

// Router::get('/error/{code}', 'ErrorController@error', $authMiddleware);
// Router::get('', 'ErrorController@index');

$request = new Request($_SERVER, $_GET, $_POST);
Router::dispatch($request);
