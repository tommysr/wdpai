<?php

require_once "AppController.php";
require_once __DIR__ . '/../models/Quest.php';
require_once __DIR__ . '/../repository/QuestRepository.php';
require_once __DIR__ . '/../repository/UserRepository.php';

class QuestsController extends AppController
{
  private $questRepository;
  private $userRepository;

  public function __construct()
  {
    parent::__construct();
    $this->questRepository = new QuestRepository();
    $this->userRepository = new UserRepository();
  }

  public function quests()
  {
    $quests = $this->questRepository->getQuests();
    $this->render('quests', ['title' => 'quest list', 'quests' => $quests]);
  }

  public function enterQuest($quest_id)
  {    
    session_start();

    if (!isset($_SESSION['email'])) {
      $url = "http://$_SERVER[HTTP_HOST]";
      header("Location: {$url}/login");
      return;
    }

    if (!$this->userCanEnterQuest($_SESSION['email'], $quest_id)) {
      return $this->quests();
    }

    if (!$this->isPost()) {
      return $this->render('enterQuest', ['title' => 'enter quest']);
    }


    echo "Success";
  }

  private function userCanEnterQuest($userEmail, $questId)
  {
    return !$this->userRepository->hasUserParticipatedInQuest($userEmail, $questId);
  }
}