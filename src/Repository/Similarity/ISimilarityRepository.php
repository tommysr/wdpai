<?php

namespace App\Repository\Similarity;

interface ISimilarityRepository
{
  public function getSimilarityMatrix(): array;
  public function saveSimilarityMatrix(array $matrix): void;
}