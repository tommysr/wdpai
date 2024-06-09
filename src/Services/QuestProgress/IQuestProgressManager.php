<?php

namespace App\Services\QuestProgress;


interface IQuestProgressManager
{
  public function resetSession(): void;
  public function startProgress(int $questId, int $walletId): void;
  public function completeQuest(): void;
  public function abandonQuest(): void;
  public function addPoints(int $pointsGained): void;
  public function changeProgress(int $questionId): void;
  public function recordResponses(int $userId, array $selectedOptions): void;
}
