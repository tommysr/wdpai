<?php

namespace App\Services\Quests;

interface IQuestResult
{
  public function getMessages(): array;

  public function getQuests(): array;

  public function isSuccess(): bool;
}