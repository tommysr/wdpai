<?php

namespace App\Services\Quests;

use App\Models\IQuest;
use App\Result\IResult;
use App\Services\Authenticate\IIdentity;

interface IQuestService
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
  public function publishQuest(int $questId): void;
  public function unpublishQuest(int $questId): void;
  public function createQuest(IQuest $quest): void;
  public function editQuest(IQuest $quest): void;
  public function addParticipant(int $questId): bool;
}