<?php

namespace App\Services\Recommendation;
use App\Services\Recommendation\IVector;

interface ISimilarityStrategy
{
  public function calculate(IVector $firstVector, IVector $secondVector): float;
}