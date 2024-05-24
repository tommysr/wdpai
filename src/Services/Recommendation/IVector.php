<?php

namespace App\Services\Recommendation;

interface IVector
{
  public function norm(): float;
  public function dot(IVector $other): float;
  public function getDimensionValue(int $i): float;
  public function getDimensions(): int;
}