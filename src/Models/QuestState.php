<?php

namespace App\Models;

use App\Models\Interfaces\IQuestState;

enum QuestState: int implements IQuestState
{
  case InProgress = 1;
  case Finished = 2;
  case Abandoned = 3;

  public function getStateId(): int
  {
    return match ($this) {
      self::InProgress => 1,
      self::Finished => 2,
      self::Abandoned => 3,
    };
  }

  public static function fromId(int $stateId): IQuestState
  {
    return match ($stateId) {
      1 => self::InProgress,
      2 => self::Finished,
      3 => self::Abandoned,
    };
  }
}