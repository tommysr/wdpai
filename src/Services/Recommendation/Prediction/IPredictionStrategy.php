<?php

namespace App\Services\Recommendation\Prediction;

interface IPredictionStrategy
{
  public function predict(int $userId, int $itemId): float;
}