<?php

namespace App\Services\Recommendation;

interface IRecommendationService
{
  public function refreshRecommendations(): void;
  public function getRecommendations(int $userId): array;
}