<?php 

require_once 'src/controllers/AppController.php';


$controller = new AppController();

$controller->render("dashboard");
