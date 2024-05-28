<?php

namespace App\Services\Authorize\Quest;

enum QuestRequest
{
  case ACCESS;
  case EDIT;


  public static function from(string $action): ?self
  {
    switch ($action) {
      case 'editQuest':
        return self::EDIT;
      case 'showQuests':
      case 'enterQuest':
        return self::ACCESS;
      default:
        return null;
    }
  }
}


