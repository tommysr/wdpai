<?php

namespace App\Services\Recommendation;

interface IPredictionStrategy
{
  public function predict(int $id, IVector $ratings): array;
}