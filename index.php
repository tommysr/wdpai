<?php

require_once 'src/controllers/AppController.php';
require_once 'src/models/Quest.php';
require_once 'Database.php';

$controller = new AppController();

$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url( $path, PHP_URL_PATH);
$action = explode("/", $path)[0];
$action = $action == null ? 'login': $action;

switch($action){
    case "dashboard":
        $db = new Database();
        $stmt = $db->connect()->prepare('SELECT * FROM public.quests');
        $stmt->execute();
        $quests = [];


        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $quest = new Quest($row['questid'], $row['title'], $row['description'], $row['worthknowledge'], $row['requiredwallet'], $row['timerequired'], $row['expirydate'], $row['participantscount'], $row['participantlimit'], $row['poolamount']);
            $quests[] = $quest;
        }
   
        $controller->render($action, ["title"=> "Quests page", "items" => $quests]);
        break;
    case "login":
        $controller->render($action, ["title"=> "Login page"]);
        break;
    default:
        $controller->render($action);
        break;
}