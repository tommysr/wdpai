<?php

namespace App\Services\Quest;

use App\Models\IQuest;
use App\Services\Authenticate\IIdentity;

interface IQuestProvider
{
  // left for optimization
  public function getCreatorQuests(IIdentity $identity): array;
  public function getTopRatedQuests(): array;
  public function getQuestsByIds(array $questIds): array;
  // business logic controlling which ones are playable
  public function getQuestsToPlay(): array;
  // leave for optimization of queries
  public function getQuestsToApproval(): array;
  // leave for optimization of queries
  public function getApprovedQuests(): array;
  public function getQuestWithQuestions(int $questId): ?IQuest;
  public function getQuest(int $questId): ?IQuest;
}