<?php

namespace App\Services\Recommendation\Data;

use App\Services\Recommendation\Data\IDataManager;

class DataManager implements IDataManager
{
  protected array $data = [];
  protected array $similarityMatrix = [];

  public function __construct(array $data = [], array $similarityMatrix = [])
  {
    $this->data = $data;
    $this->similarityMatrix = $similarityMatrix;
  }

  public function setData(array $data): void
  {
    $this->data = $data;
  }

  public function getData(): array
  {
    return $this->data;
  }

  public function setSimilarityMatrix(array $similarityMatrix): void
  {
    $this->similarityMatrix = $similarityMatrix;
  }

  public function getSimilarityMatrix(): array
  {
    return $this->similarityMatrix;
  }
}