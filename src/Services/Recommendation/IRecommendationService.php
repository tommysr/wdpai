<?php

namespace App\Services\Recommendation;

interface IRecommendationService
{
  public function getRecommendations(int $userId): array;
  public function getPopularItems(): array;
}