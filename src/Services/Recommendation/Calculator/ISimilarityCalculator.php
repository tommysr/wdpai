<?php

namespace App\Services\Recommendation\Calculator;

interface ISimilarityCalculator
{
  public function calculate(): array;
}