<?php

namespace App\Models\Interfaces;

use App\Models\Interfaces\IQuestState;

/**
 * Interface for quest statistics.
 */
interface IQuestProgress
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


  public function getState(): IQuestState;

  public function isCompleted(): bool;

  public function setState(IQuestState $state): void;

  public function setScore(int $score): void;

  public function setNextQuestionId(int $questionId): void;

  public function setCompletionDateToNow(): void;
}