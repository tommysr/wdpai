<?php
require "Routing.php";

$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path, PHP_URL_PATH);

Router::get('', 'QuestsController');

Router::get('login', 'AuthController');
Router::get('logout', 'AuthController');
Router::get('register', 'AuthController');

Router::get('quests', 'QuestsController');
Router::get('enterQuest', 'QuestsController');
Router::get('gameplay', 'GameController');
Router::get('processUserResponse', 'GameController');
Router::get('nextQuestion', 'GameController');
Router::run($path);
