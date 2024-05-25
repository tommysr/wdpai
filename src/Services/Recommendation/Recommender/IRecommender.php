<?php

namespace App\Services\Recommendation\Recommender;

use App\Services\Recommendation\Prediction\IPredictionStrategy;
use App\Services\Recommendation\Similarity\ISimilarityStrategy;

interface IRecommender
{
  public function construct(): self;
  public function estimate(int $index): array;
  public function calculateSimilarityMatrix(): void;
  public function getSimilarityMatrix(): array;
  public function setPredictionStrategy(IPredictionStrategy $strategy): void;
  public function setSimilarityStrategy(ISimilarityStrategy $strategy): void;
}