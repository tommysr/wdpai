<?php

namespace App\Services\Quests;

use App\Models\IQuest;
use App\Services\Authenticate\IIdentity;
use App\Services\Quests\IQuestResult;

interface IQuestService
{
  public function getCreatorQuests(IIdentity $identity): array;
  public function getTopRatedQuests(): array;
  public function getQuests(array $questIds): array;
  public function getQuestsToPlay(): array;
  public function getQuestsToApproval(): array;
  public function getApprovedQuests(): array;
  public function getQuestBlockchain(int $questId): string;
  public function getQuestWithQuestions(int $questId): ?IQuest;
  public function getQuest(int $questId): ?IQuest;
  public function publishQuest(int $questId): void;
  public function unpublishQuest(int $questId): void;
  public function createQuest(IQuest $quest): IQuestResult;
  public function editQuest(IQuest $quest): IQuestResult;
  public function addParticipant(int $questId): bool;
}