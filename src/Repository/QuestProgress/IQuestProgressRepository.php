<?php

namespace App\Repository\QuestProgress;

use App\Models\Interfaces\IQuestProgress;

interface IQuestProgressRepository
{
  public function saveQuestProgress(IQuestProgress $questProgress);
  public function updateQuestProgress(IQuestProgress $questProgress);
  public function getQuestProgress(int $userId, int $questId): ?IQuestProgress;
  public function getInProgress(int $userId): ?IQuestProgress;
  public function getAllProgresses(int $questId): array;
  public function saveResponses(int $userId, array $responses): void;
  public function getResponsesCount(int $optionId): int;
  public function getPercentileRank(int $userId, int $questId): int;
  public function getUserEntries(int $userId): array;
}