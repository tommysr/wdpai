<?php

namespace App\Models\Interfaces;

interface IQuestState
{
  public function getStateId(): int;

  public static function fromId(int $stateId): IQuestState;
}