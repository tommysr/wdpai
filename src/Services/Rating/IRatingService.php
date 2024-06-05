<?php

namespace App\Services\Rating;
use App\Models\Interfaces\IRating;

interface IRatingService
{
  public function addRating(IRating $rating): void;
  public function getUserItemMatrix(): array;
}