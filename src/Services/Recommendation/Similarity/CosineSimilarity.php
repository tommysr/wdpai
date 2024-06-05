<?php

namespace App\Services\Recommendation\Similarity;

use App\Services\Recommendation\Similarity\ISimilarityStrategy;
use App\Services\Recommendation\Utils\IVector;

class CosineSimilarity implements ISimilarityStrategy
{
  public function calculate(IVector $firstVector, IVector $secondVector): float
  {
    $dotProduct = $firstVector->dot($secondVector);
    $magnitude1 = $firstVector->norm();
    $magnitude2 = $secondVector->norm();
    $denominator = $magnitude1 * $magnitude2;


    $ret = 0.0;
    if ($denominator != 0) {
      $ret = $dotProduct / $denominator;
    }

    return $ret;
  }
}