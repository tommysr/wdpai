<?php

namespace App\Services\Quests;

use App\Models\IQuest;
use App\Services\Authenticate\IIdentity;
use App\Services\Quests\IQuestResult;

interface IQuestService
{
  public function getCreatorQuests(IIdentity $identity): array;
  public function getQuestsToPlay(): array;
  public function getQuestsToApproval(): array;
  public function getQuestWallets(IIdentity $identity, int $questId): array;
  public function getQuestWithQuestions(int $questId): ?IQuest;
  public function getQuest(int $questId): ?IQuest;
  public function publishQuest(int $questId): void;
  public function createQuest(array $data, int $creatorId): IQuestResult;
  public function editQuest(array $data, int $creatorId, int $questId): IQuestResult;
}