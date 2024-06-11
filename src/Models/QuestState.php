<?php

namespace App\Models;

use App\Models\Interfaces\IQuestState;

enum QuestState: int implements IQuestState
{
  case InProgress = 1;
  case Unrated = 2;
  case Rated = 3;
  case Finished = 4;
  case Abandoned = 5;

  public function getStateId(): int
  {
    return match ($this) {
      self::InProgress => 1,
      self::Unrated => 2,
      self::Rated => 3,
      self::Finished => 4,
      self::Abandoned => 5,
    };
  }

  public static function fromId(int $stateId): IQuestState
  {
    return match ($stateId) {
      1 => self::InProgress,
      2 => self::Unrated,
      3 => self::Rated,
      4 => self::Finished,
      5 => self::Abandoned,
    };
  }
}