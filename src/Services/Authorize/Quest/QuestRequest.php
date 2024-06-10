<?php

namespace App\Services\Authorize\Quest;

enum QuestRequest: string
{
  case ACCESS = 'access';
  case EDIT = 'edit';


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


