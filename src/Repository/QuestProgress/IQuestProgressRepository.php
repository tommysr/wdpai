<?php

namespace App\Repository\QuestProgress;

use App\Models\Interfaces\IQuestProgress;

interface IQuestProgressRepository
{
  public function saveQuestProgress(IQuestProgress $questProgress): int;
  public function updateQuestProgress(IQuestProgress $questProgress);
  public function getQuestProgress(int $userId, int $questId): ?IQuestProgress;
  public function getInProgress(int $userId): ?int;
}