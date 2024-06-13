<?php

namespace App\Services\Recommendation\Prediction;

use App\Services\Recommendation\Data\IDataManager;
use App\Services\Recommendation\Prediction\IPredictionStrategy;
use App\Services\Recommendation\Prediction\BasePredictionStrategy;



class KnnPredictor implements IPredictionStrategy
{
  private int $k;
  private IDataManager $dataManager;

  public function __construct(IDataManager $dataManager, int $k = 3)
  {
    $this->dataManager = $dataManager;
    $this->k = $k;
  }


  private function getKNearestNeighbors(int $userIndex, int $k): array
  {
    $similarities = $this->dataManager->getSimilarityMatrix()[$userIndex];
    asort($similarities);
    $neighbors = array_slice(array_keys($similarities), 1, $k, true);

    return $neighbors;
  }


  public function predict(int $userId, int $itemId): float
  {
    $neighbors = $this->getKNearestNeighbors($userId, $this->k);
    $data = $this->dataManager->getData();
    $similarityMatrix = $this->dataManager->getSimilarityMatrix();
    $userRatings = $data[$userId];


    if ($userRatings[$itemId] != 0) {
      return 0.0;
    }

    $weightedSum = 0.0;
    $similaritySum = 0.0;


    foreach ($neighbors as $neighbor) {
      if ($data[$neighbor][$itemId] != 0) {

        $weightedSum += $similarityMatrix[$userId][$neighbor] * $data[$neighbor][$itemId];
        $similaritySum += abs($similarityMatrix[$userId][$neighbor]);
      }
    }


    if ($similaritySum != 0) {
      return $weightedSum / $similaritySum;
    } else {
      return 0.0;
    }
  }
}