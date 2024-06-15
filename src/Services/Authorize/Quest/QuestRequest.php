<?php

namespace App\Services\Authorize\Quest;

enum QuestRequest: string
{
  case ACCESS = 'access';
  case EDIT = 'edit';

  public static function fromAction(string $action): ?self
  {
    switch ($action) {
      case 'editQuest':
      case 'showEditQuest':
        return self::EDIT;
      case 'showQuests':
      case 'enterQuest':
      case 'showQuestWallets':
        return self::ACCESS;
      default:
        return null;
    }
  }
}


