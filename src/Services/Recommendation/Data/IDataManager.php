<?php

namespace App\Services\Recommendation\Data;

interface IDataManager
{
  public function getData(): array;
  public function setData(array $data): void;
  public function setSimilarityMatrix(array $similarityMatrix): void;
  public function getSimilarityMatrix(): array;
}