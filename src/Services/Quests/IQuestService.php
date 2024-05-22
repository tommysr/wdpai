<?php

namespace App\Services\Quests;

use App\Models\Quest;
use App\Models\IQuest;
use App\Services\Authenticate\IIdentity;
use App\Services\Quests\IQuestResult;

interface IQuestService
{
  public function getQuests(string $role): array;
  // public function saveQuest(IQuest $quest): IQuest;
  public function getQuestsToPlay(): array;
  public function getQuestWithQuestions(int $questId): ?IQuest;

  public function getCreatorQuests(IIdentity $identity): array;

  public function getQuestsToApproval(): array;

  public function publishQuest(int $questId): void;

  public function getQuest(int $questId): ?IQuest;

  public function createQuest(array $data, int $creatorId): IQuestResult;
  // public function updateQuest(int $id, array $data): Quest;
  // public function deleteQuest(int $id): void;
}