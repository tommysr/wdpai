<?php

namespace App\Models;

/**
 * Interface for quest statistics.
 */
interface IQuestStatistics
{
  /**
   * Get the completion date of the quest.
   * 
   * @return string|null The completion date of the quest.
   */
  public function getCompletionDate(): ?string;

  /**
   * Get the score of the quest.
   * 
   * @return int The score of the quest.
   */
  public function getScore(): int;

  /**
   * Get the user ID of the quest.
   * 
   * @return int The user ID of the quest.
   */
  public function getUserId(): int;

  /**
   * Get the quest ID of the quest.
   * 
   * @return int The quest ID of the quest.
   */
  public function getQuestId(): int;

  /**
   * Get the wallet ID of the quest.
   * 
   * @return int The wallet ID of the quest.
   */
  public function getWalletId(): int;

  /**
   * Get the last question ID of the quest.
   * 
   * @return int The last question ID of the quest.
   */
  public function getLastQuestionId(): int;

  /**
   * Get the state of the quest.
   * 
   * @return string The state of the quest.
   */
  public function getState(): string;
}