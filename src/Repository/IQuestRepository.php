<?php

namespace App\Repository;

use App\Models\IQuest;

interface IQuestRepository
{
  public function saveQuest(IQuest $quest): int;
  public function getQuestById($questId): ?IQuest;
  public function getQuests(): array;
  public function getCreatorQuests(int $creator): array;
  public function getApprovedQuests(): array;
  public function getQuestToApprove(): array;
  public function approve(int $questId);
  public function updateQuest(IQuest $quest);
  public function getAllQuestIds(): array;
  public function getMaxQUestId(): int;
}