<?php

namespace App\Models;

use App\Models\Interfaces\IRating;

class Rating implements IRating
{
  private int $userId;
  private int $questId;
  private int $rating;

  public function __construct(int $userId, int $questId, int $rating)
  {
    $this->userId = $userId;
    $this->questId = $questId;
    $this->rating = $rating;
  }

  public function getRating(): int
  {
    return $this->rating;
  }

  public function getQuestId(): int
  {
    return $this->questId;
  }

  public function getUserId(): int
  {
    return $this->userId;
  }
}