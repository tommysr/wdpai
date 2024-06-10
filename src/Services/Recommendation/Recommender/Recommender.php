<?php

namespace App\Services\Recommendation\Recommender;

use App\Services\Recommendation\Recommender\IRecommender;
use App\Services\Recommendation\Similarity\ISimilarityStrategy;
use App\Services\Recommendation\Prediction\IPredictionStrategy;
use App\Services\Recommendation\Data\IDataManager;
use App\Services\Recommendation\Calculator\SimilarityCalculator;
use App\Services\Recommendation\Utils\IVector;
use App\Services\Recommendation\Utils\Vector;

class Recommender implements IRecommender
{
  protected IVector $vector;
  protected IDataManager $dataManager;
  protected ISimilarityStrategy $similarityStrategy;
  protected IPredictionStrategy $predictionStrategy;

  public function __construct(IDataManager $dataManager)
  {
    $this->dataManager = $dataManager;
  }

  public function setPredictionStrategy(IPredictionStrategy $strategy): void
  {
    $this->predictionStrategy = $strategy;
  }

  public function setSimilarityStrategy(ISimilarityStrategy $strategy): void
  {
    $this->similarityStrategy = $strategy;
  }

  public function setSimilarityMatrix(array $similarityMatrix): void
  {
    $this->similarityMatrix = $similarityMatrix;
  }

  public function calculateSimilarityMatrix(): void
  {
    $data = $this->dataManager->getData();

    $userCount = count($data);
    $similarityMatrix = [];

    for ($i = 0; $i < $userCount; $i++) {
      for ($j = 0; $j < $userCount; $j++) {
        if ($i == $j) {
          $similarityMatrix[$i][$j] = 1.0;
        } else {

          $vector1 = Vector::fromArr($data[$i]);
          $vector2 = Vector::fromArr($data[$j]);


          $similarityMatrix[$i][$j] = $this->similarityStrategy->calculate($vector1->intersect($vector2), $vector2->intersect($vector1));
        }
      }
    }

    $this->dataManager->setSimilarityMatrix($similarityMatrix);
  }

  public function construct(): self
  {
    if (empty($this->dataManager->getData())) {
      throw new \Exception('empty data');
    }

    $this->calculateSimilarityMatrix();


    return $this;
  }

  public function estimate(int $userIndex): array
  {
    if (empty($this->dataManager->getSimilarityMatrix())) {
      throw new \Exception('no similarities calculated');
    }

    $itemCount = count($this->dataManager->getData()[0]);
    $predictions = [];

    for ($item = 0; $item < $itemCount; $item++) {
      $predictions[] = $this->predictionStrategy->predict($userIndex, $item);
    }

    return $predictions;
  }

  public function getSimilarityMatrix(): array
  {
    return $this->dataManager->getSimilarityMatrix();
  }
}


