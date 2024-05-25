<?php

namespace App\Services\Recommendation\Prediction;

use App\Services\Recommendation\Prediction\IPredictionStrategy;

abstract class BasePredictionStrategy implements IPredictionStrategy
{
  protected array $data;
  protected array $similarityMatrix;

  public function __construct(array $data, array $similarityMatrix)
  {
    $this->data = $data;
    $this->similarityMatrix = $similarityMatrix;
  }
}