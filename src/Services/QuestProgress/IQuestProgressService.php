<?php

namespace App\Services\QuestProgress;

use App\Models\Interfaces\IQuestProgress;
use App\Models\IQuestion;
use App\Services\Authenticate\IIdentity;


interface IQuestProgressService
{
  public function resetSession(): void;
  public function startProgress(int $questId, int $walletId): void;
  public function getQuestion(int $questId, int $questionId): ?IQuestion;
  public function getCurrentProgress(int $userId, int $questId): IQuestProgress;
  public function getCurrentProgressFromSession(): IQuestProgress;
  public function recordResponses(int $userId, array $selectedOptions): void;
  public function getResponsesCount(int $optionId): int;
  public function updateQuestProgress(int $pointsGained): void;
  public function completeQuest(): void;
  public function getMaxScore(int $questId): int;
  public function evaluateOptions(int $questionId, array $selectedOptions): array;
  public function abandonQuest(): void;
  public function adjustQuestProgress(int $questionId): void;
  public function getQuestSummary(int $userId): array;
  
  public function getUserQuests(int $userId): array;
  public function isQuestPlayed(int $userId, int $questId): bool;
}