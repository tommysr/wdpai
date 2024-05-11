<?php

class QuestStatistics
{
  private string|null $completion_date;
  private int|null $score;
  private $user_id;
  private $quest_id;
  private $wallet_id;

  public function __construct(string|null $completion_date, int|null $score, int $user_id, int $quest_id, int$wallet_id)
  {
    $this->completion_date = $completion_date;
    $this->score = $score;
    $this->user_id = $user_id;
    $this->quest_id = $quest_id;
    $this->wallet_id = $wallet_id;
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