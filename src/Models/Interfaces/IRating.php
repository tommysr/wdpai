<?php

namespace App\Models\Interfaces;

interface IRating
{
  public function getRating(): int;
  public function getQuestId(): int;
  public function getUserId(): int;
}