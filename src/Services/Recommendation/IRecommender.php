<?php

namespace App\Services\Recommendation;

interface IRecommender
{
  public function construct(): self;
  public function setData(array $data): void;
  public function setSimilarityMatrix(array $similarityMatrix): void;
  public function estimate(int $index): array;
  public function getSimilarityMatrix(): array;
  public function setPredictionStrategy(string $strategy, array $data): void;
  public function setSimilarityStrategy(string $strategy, array $data): void;
}