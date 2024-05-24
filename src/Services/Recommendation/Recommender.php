<?php

namespace App\Services\Recommendation;

use App\Services\Recommendation\IRecommender;
use App\Services\Recommendation\ISimilarityStrategy;
use App\Services\Recommendation\KnnPredictor;

class Recommender implements IRecommender
{
  protected array $data = [];
  protected array $similarityMatrix = [];
  protected ISimilarityStrategy $similarityStrategy;
  protected IPredictionStrategy $predictionStrategy;

  public function setPredictionStrategy(string $name, array $data): void
  {
    if (empty($this->data) || empty($this->similarityMatrix)) {
      throw new \Exception('Data is not set');
    }

    switch ($name) {
      case 'knn':
        $this->predictionStrategy = new KnnPredictor($this->data, $this->similarityMatrix, $data['k']);
        break;
      default:
        throw new \Exception('Invalid prediction strategy');
    }
  }

  public function setSimilarityStrategy(string $name, array $data): void
  {
    if (empty($this->data) || empty($this->similarityMatrix)) {
      throw new \Exception('Data is not set');
    }

    switch ($name) {
      case 'cosine':
        $this->similarityStrategy = new CosineSimilarity();
        break;
      default:
        throw new \Exception('Invalid similarity strategy');
    }
  }

  public function setData(array $data): void
  {
    $this->data = $data;
  }

  public function setSimilarityMatrix(array $similarityMatrix): void
  {
    $this->similarityMatrix = $similarityMatrix;
  }

  private function calculateSimilarityMatrix(): void
  {
    $userCount = count($this->data);
    $similarityMatrix = [];

    for ($i = 0; $i < $userCount; $i++) {
      for ($j = 0; $j < $userCount; $j++) {
        if ($i == $j) {
          $similarityMatrix[$i][$j] = 1.0;
        } else {
          $vector1 = new Vector($this->data[$i]);
          $vector2 = new Vector($this->data[$j]);
          $similarityMatrix[$i][$j] = $this->similarityStrategy->calculate($vector1, $vector2);
        }
      }
    }

    $this->similarityMatrix = $similarityMatrix;
  }

  public function construct(): self
  {
    if (empty($this->similarityMatrix)) {
      $this->calculateSimilarityMatrix();
    }

    return $this;
  }

  public function estimate(int $userIndex): array
  {
    $userRatings = $this->data[$userIndex];

    return $this->predictionStrategy->predict($userIndex, $userRatings);
  }

  public function getSimilarityMatrix(): array
  {
    return $this->similarityMatrix;
  }
}
