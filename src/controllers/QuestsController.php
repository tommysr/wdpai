<?php

require_once "AppController.php";
require_once __DIR__ . '/../models/Quest.php';
require_once __DIR__ . '/../repository/QuestRepository.php';
require_once __DIR__ . '/../repository/QuestStatisticsRepository.php';

class QuestsController extends AppController
{
  private $questRepository;
  private $questStatisticsRepository;

  public function __construct()
  {
    parent::__construct();
    $this->questRepository = new QuestRepository();
    $this->questStatisticsRepository = new QuestStatisticsRepository();
  }

  public function quests()
  {
    $quests = $this->questRepository->getQuests();
    $this->render('quests', ['title' => 'quest list', 'quests' => $quests]);
  }

  public function enterQuest($quest_id)
  {
    session_start();

    $user_id = $_SESSION['user_id'];

    if (!isset($user_id)) {
      $url = "http://$_SERVER[HTTP_HOST]";
      header("Location: {$url}/login");
      return;
    }

    if (!$this->userCanEnterQuest($user_id, $quest_id)) {
      return $this->quests();
    }

    if (!$this->isPost()) {
      return $this->render('enterQuest', ['title' => 'enter quest']);
    }


    echo "Success";
  }

  private function userCanEnterQuest($userId, $questId)
  {
    if ($this->questStatisticsRepository->getQuestStatistic($userId, $questId)) {
      return false;
    }

    return true;
  }
}