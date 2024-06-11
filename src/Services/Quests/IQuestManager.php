<?php

namespace App\Services\Quests;

use App\Models\IQuest;

interface IQuestManager
{
  public function publishQuest(int $questId): void;
  public function unpublishQuest(int $questId): void;
  public function createQuest(IQuest $quest): void;
  public function editQuest(IQuest $quest): void;
  public function addParticipant(int $questId): bool;
}