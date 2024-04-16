<?php

require_once "AppController.php";
require_once __DIR__ . '/../models/Quest.php';
require_once __DIR__ . '/../repository/QuestRepository.php';


class QuestsController extends AppController
{
  private $questRepository;

  public function __construct()
  {
    parent::__construct();
    $this->questRepository = new QuestRepository();
  }

  public function quests()
  {
    $quests = $this->questRepository->getQuests();
    $this->render('quests', ['title' => 'quest list', 'quests' => $quests]);
  }
}