<?php

namespace App\Services\Recommendation;

use App\Services\Recommendation\IPredictionStrategy;
use App\Services\Recommendation\BasePredictionStrategy;

class KnnPredictor extends BasePredictionStrategy implements IPredictionStrategy
{
  private $k;

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


  public function predict(int $id, IVector $ratings): array
  {
    $neighbors = $this->getKNearestNeighbors($id, $this->k);
    $userRatings = $this->data[$id];

    $predictedRatings = [];
    $itemCount = count($this->data[0]);

    for ($item = 0; $item < $itemCount; $item++) {
      if ($userRatings[$item] == 0) {
        $weightedSum = 0.0;
        $similaritySum = 0.0;

        foreach ($neighbors as $neighbor) {
          if ($this->data[$neighbor][$item] != 0) {
            $weightedSum += $this->similarityMatrix[$id][$neighbor] * $this->data[$neighbor][$item];
            $similaritySum += abs($this->similarityMatrix[$id][$neighbor]);
          }
        }

        if ($similaritySum != 0) {
          $predictedRatings[$item] = $weightedSum / $similaritySum;
        } else {
          $predictedRatings[$item] = 0;
        }
      } else {
        $predictedRatings[$item] = $userRatings[$item];
      }
    }

    return $predictedRatings;
  }
}