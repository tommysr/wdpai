<?php

namespace App\Services\Quests;

use App\Services\Quests\IQuestResult;

class QuestResult implements IQuestResult
{
  private array $messages;
  private array $quests;

  private bool $isSuccess;

  public function __construct(array $messages = [], array $quests = [], bool $isSuccess = false)
  {
    $this->messages = $messages;
    $this->quests = $quests;
    $this->isSuccess = $isSuccess;
  }

  public function getMessages(): array
  {
    return $this->messages;
  }

  public function getQuests(): array
  {
    return $this->quests;
  }

  public function isSuccess(): bool
  {
    return $this->isSuccess;
  }
}