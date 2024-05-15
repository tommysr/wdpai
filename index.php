<?php
require "Routing.php";

$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path, PHP_URL_PATH);


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

$middleware = new RoleAuthorizationMiddleware(new UserAuthorizationService(new SessionService()), Role::USER);
$middleware->setNext(new QuestAuthorizationMiddleware(new QuestAuthorizeService(new QuestStatisticsRepository()), QuestRequest::PLAY));

Router::get('/admin', $middleware, 'AdminController@index');

$userLoggedInMiddleware = new RoleAuthorizationMiddleware(new UserAuthorizationService(new SessionService()), Role::USER);

Router::get('login', 'AuthController');
Router::get('logout', 'AuthController');
Router::get('register', 'AuthController');

Router::run($path);
