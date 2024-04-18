<?php

class QuestStatistics
{
  private $completion_date;
  private $score;
  private $user_id;
  private $quest_id;

  public function __construct($completion_date, $score, $user_id, $quest_id)
  {
    $this->completion_date = $completion_date;
    $this->score = $score;
    $this->user_id = $user_id;
    $this->quest_id = $quest_id;
  }

  public function getCompletionDate()
  {
    return $this->completion_date;
  }

  public function getScore()
  {
    return $this->score;
  }

  public function getUserId()
  {
    return $this->user_id;
  }

  public function getQuestId()
  {
    return $this->quest_id;
  }
}
;