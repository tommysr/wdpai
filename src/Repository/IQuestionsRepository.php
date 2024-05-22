<?php

namespace App\Repository;

use App\Models\IQuest;

interface IQuestRepository
{
  public function getQuests(): array;
  public function getQuest(int $id): IQuest;
  public function createQuest(IQuest $quest): void;
  public function updateQuest(IQuest $quest): void;
  public function deleteQuest(int $id): void;
}