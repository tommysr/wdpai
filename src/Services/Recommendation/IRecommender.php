<?php

namespace App\Services\Recommendation;

interface IRecommender
{
  public function construct(): self;
  public function estimate(int $index): array;
}