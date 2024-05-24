<?php

namespace App\Services\Recommendation;

class RecommenderBuilder
{
  private $data = [];
  private $similarityMatrix = [];
  private $predictionStrategy;
  private $similarityStrategy;
  private $predictionData;
  private $similarityData;

  public function setData(array $data): self
  {
    $this->data = $data;
    return $this;
  }

  public function setSimilarityMatrix(array $similarityMatrix): self
  {
    $this->similarityMatrix = $similarityMatrix;
    return $this;
  }

  public function setPredictionStrategy(string $predictionStrategy, array $data): self
  {
    $this->predictionStrategy = $predictionStrategy;
    $this->predictionData = $data;

    return $this;
  }

  public function setSimilarityStrategy(string $similarityStrategy, array $data): self
  {
    $this->similarityStrategy = $similarityStrategy;
    $this->similarityData = $data;

    return $this;
  }

  public function build(): IRecommender
  {
    $recommender = new Recommender();
    $recommender->construct();
    $recommender->setData($this->data);
    $recommender->setSimilarityMatrix($this->similarityMatrix);
    $recommender->setPredictionStrategy($this->predictionStrategy, $this->data);
    $recommender->setSimilarityStrategy($this->similarityStrategy, $this->data);
    return $recommender;
  }
}
