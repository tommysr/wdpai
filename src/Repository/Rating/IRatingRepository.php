<?php

namespace App\Repository\Rating;

use App\Models\Interfaces\IRating;

interface IRatingRepository
{
  public function getRatings(): array;
  public function getRating(int $userId, int $questId): ?IRating;
  public function addRating(IRating $rating): void;
  public function updateRating(IRating $rating): void;
  public function deleteRating(int $userId, int $questId): void;
}