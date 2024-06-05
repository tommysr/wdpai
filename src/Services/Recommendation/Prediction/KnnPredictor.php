<?php

namespace App\Services\Recommendation\Prediction;

use App\Services\Recommendation\Prediction\IPredictionStrategy;
use App\Services\Recommendation\Prediction\BasePredictionStrategy;

class KnnPredictor extends BasePredictionStrategy implements IPredictionStrategy
{
  private int $k;

  public function __construct(array $data, array $similarityMatrix, int $k = 3)
  {
    parent::__construct($data, $similarityMatrix);
    $this->k = $k;
  }


  private function getKNearestNeighbors(int $userIndex, int $k): array
  {
    $similarities = $this->similarityMatrix[$userIndex];
    asort($similarities);
    $neighbors = array_slice(array_keys($similarities), 1, $k, true);

    return $neighbors;
  }


  public function predict(int $userId, int $itemId): float
  {
    $neighbors = $this->getKNearestNeighbors($userId, $this->k);

   
    $userRatings = $this->data[$userId];


    if ($userRatings[$itemId] != 0) {
      return 0.0;
    }

    $weightedSum = 0.0;
    $similaritySum = 0.0;


    foreach ($neighbors as $neighbor) {
      if ($this->data[$neighbor][$itemId] != 0) {
        
        $weightedSum += $this->similarityMatrix[$userId][$neighbor] * $this->data[$neighbor][$itemId];
        $similaritySum += abs($this->similarityMatrix[$userId][$neighbor]);
      }
    }


    if ($similaritySum != 0) {
      return $weightedSum / $similaritySum;
    } else {
      return 0.0;
    }
  }
}