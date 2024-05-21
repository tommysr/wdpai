<?php

namespace App\Repository;

use App\Models\IQuestStatistics;

/**
 * Interface for quest statistics repository.
 */

interface IQuestStatisticsRepository
{
  /**
   * Save quest statistics.
   * 
   * @param IQuestStatistics $questStatistics The quest statistics.
   * @return int The ID of the saved quest statistics.
   */
  public function saveQuestStatistics(IQuestStatistics $questStatistics): int;

  /**
   * Get all quest statistics.
   * 
   * @return array The quest statistics.
   */
  public function getQuestStatistics(int $userId, int $questId): ?IQuestStatistics;

  /**
   * Update quest statistics.
   * 
   * @param IQuestStatistics $questStatistics The quest statistics.
   */
  public function updateQuestStatistics(IQuestStatistics $questStatistics);

  public function getQuestIdToFinish(int $userId): ?int;

}