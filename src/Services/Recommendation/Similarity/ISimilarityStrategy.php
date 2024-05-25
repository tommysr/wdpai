<?php

namespace App\Services\Recommendation\Similarity;
use App\Services\Recommendation\Utils\IVector;

interface ISimilarityStrategy
{
  public function calculate(IVector $firstVector, IVector $secondVector): float;
}