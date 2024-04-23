<?php

require_once __DIR__ . '/../repository/QuestionsRepository.php';

class GameService
{
  private $questionRepository;

  public function __construct()
  {
    $this->questionRepository = new QuestionsRepository();
  }

  public function getQuestionsForQuest($questId)
  {
  }

  public function processUserResponse($userId, $questId, $selectedOptions)
  {
    $score = $this->calculateScore($questId, $selectedOptions);

  }

  private function calculateScore($questId, $selectedOptions)
  {
    return 0;
  }
}
