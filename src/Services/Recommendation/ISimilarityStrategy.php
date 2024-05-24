<?php

namespace App\Services\Recommendation;

interface ISimilarityStrategy
{
  public function calculate(array $firstVector, array $secondVector): float;
}