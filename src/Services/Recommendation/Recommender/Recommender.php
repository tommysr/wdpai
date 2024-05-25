<?php

namespace App\Services\Recommendation;

use App\Services\Recommendation\Recommender\IRecommender;
use App\Services\Recommendation\Similarity\ISimilarityStrategy;
use App\Services\Recommendation\Prediction\IPredictionStrategy;
use App\Services\Recommendation\Data\IDataManager;
use App\Services\Recommendation\Calculator\SimilarityCalculator;

class Recommender implements IRecommender
{
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
    $similarityCalculator = new SimilarityCalculator($data, $this->similarityStrategy);
    $this->dataManager->setSimilarityMatrix($similarityCalculator->calculate());
  }

  public function construct(): self
  {
    if (empty($this->dataManager->getData())) {
      throw new \Exception('empty data');
    }

    if (empty($this->dataManager->getSimilarityMatrix())) {
      $this->calculateSimilarityMatrix();
    }

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
      $predictions[$item] = $this->predictionStrategy->predict($userIndex, $item);
    }

    return $predictions;
  }

  public function getSimilarityMatrix(): array
  {
    return $this->dataManager->getSimilarityMatrix();
  }
}
