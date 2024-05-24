<?php

namespace App\Services\Recommendation;

use App\Services\Recommendation\IRecommender;
use App\Services\Recommendation\ISimilarityStrategy;

class Recommender implements IRecommender
{
  protected array $data;
  protected array $similarityMatrix;
  protected ISimilarityStrategy $similarityStrategy;

  public function __construct(array $data, ISimilarityStrategy $similarityStrategy)
  {
    $this->data = $data;
    $this->similarityStrategy = $similarityStrategy;
  }

  public function construct(): IRecommender
  {
    $userCount = count($this->data);
    $similarityMatrix = [];

    for ($i = 0; $i < $userCount; $i++) {
      for ($j = 0; $j < $userCount; $j++) {
        if ($i == $j) {
          $similarityMatrix[$i][$j] = 1.0;
        } else {
          $similarityMatrix[$i][$j] = $this->similarityStrategy->calculate($this->data[$i], $this->data[$j]);
        }
      }
    }

    $this->similarityMatrix = $similarityMatrix;

    return $this;
  }

  private function getKNearestNeighbors(int $userIndex, $k): array
  {
    $similarities = $this->similarityMatrix[$userIndex];
    asort($similarities);
    $neighbors = array_slice(array_keys($similarities), 1, $k, true);

    return $neighbors;
  }


  public function estimate(int $userIndex): array
  {
    $neighbors = $this->getKNearestNeighbors($userIndex, 3);
    $userRatings = $this->data[$userIndex];
    $predictedRatings = [];
    $itemCount = count($this->data[0]);

    for ($item = 0; $item < $itemCount; $item++) {
      if ($userRatings[$item] == 0) {
        $weightedSum = 0.0;
        $similaritySum = 0.0;

        foreach ($neighbors as $neighbor) {
          if ($this->data[$neighbor][$item] != 0) {
            $weightedSum += $this->similarityMatrix[$userIndex][$neighbor] * $this->data[$neighbor][$item];
            $similaritySum += abs($this->similarityMatrix[$userIndex][$neighbor]);
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
