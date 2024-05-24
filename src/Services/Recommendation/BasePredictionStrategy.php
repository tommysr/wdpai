<?php

namespace App\Services\Recommendation;

use App\Services\Recommendation\IPredictionStrategy;

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