<?php
require "Routing.php";

$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path, PHP_URL_PATH);

Router::get('', 'QuestsController');
Router::get('quests', 'QuestsController');
Router::get('showQuestWallets', 'QuestsController');
Router::get('startQuest', 'QuestsController');

Router::get('createQuest', 'QuestsController');
Router::get('editQuest', 'QuestsController');
Router::get('createdQuests', 'QuestsController');
Router::get('dashboard', 'QuestsController');

Router::get('login', 'AuthController');
Router::get('logout', 'AuthController');
Router::get('register', 'AuthController');

Router::get('gameplay', 'GameController');
Router::get('processUserResponse', 'GameController');
Router::get('nextQuestion', 'GameController');
Router::run($path);
