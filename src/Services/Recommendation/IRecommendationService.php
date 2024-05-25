<?php

namespace App\Services\Recommendation;

interface IRecommendationService
{
  public function refreshRecommendations(): void;
  public function getRecommendations(int $userId): array;
  public function getPopularItems(int $limit = 4): array;
}