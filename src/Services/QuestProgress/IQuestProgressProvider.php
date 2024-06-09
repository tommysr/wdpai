<?php

namespace App\Services\QuestProgress;

use App\Models\Interfaces\IQuestProgress;

interface IQuestProgressProvider
{
  public function getProgress(int $userId, int $questId): ?IQuestProgress;
  public function getCurrentProgress(): ?IQuestProgress;
  public function getQuestSummary(int $userId, int $questId): array;
  public function getUserQuests(int $userId): array;
  public function isQuestPlayed(int $userId, int $questId): bool;
  public function getResponsesCount(int $optionId): int;
}
