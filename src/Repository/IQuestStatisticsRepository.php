<?php

namespace App\Repository;
use App\Models\IQuestStatistics;


interface IQuestStatisticsRepository
{
  public function saveQuestStatistics(IQuestStatistics $questStatistics): int;
  public function getQuestStatistics(int $userId, int $questId): ?IQuestStatistics;
  public function updateQuestStatistics(IQuestStatistics $questStatistics);
  public function getQuestIdToFinish(int $userId): ?int;
}