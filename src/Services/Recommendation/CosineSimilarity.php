<?php

namespace App\Services\Recommendation;

use App\Services\Recommendation\ISimilarityStrategy;

function dotProduct($vector1, $vector2)
{
  $result = 0;
  $length = count($vector1);
  for ($i = 0; $i < $length; $i++) {
    $result += $vector1[$i] * $vector2[$i];
  }
  return $result;
}

function magnitude($vector)
{
  $sum = 0;
  foreach ($vector as $value) {
    $sum += pow($value, 2);
  }
  return sqrt($sum);
}

class CosineSimilarity implements ISimilarityStrategy
{
  public function calculate(array $firstVector, array $secondVector): float
  {
    $dotProduct = dotProduct($firstVector, $secondVector);
    $magnitude1 = magnitude($firstVector);
    $magnitude2 = magnitude($secondVector);
    $denominator = $magnitude1 * $magnitude2;

    if ($denominator != 0) {
      return $dotProduct / $denominator;
    }

    return 0.0;
  }
}