<?php

namespace App\Services\Recommendation;

use App\Services\Recommendation\IVector;

class Vector implements IVector
{
  private array $data;
  private int $dimensions;

  public function __construct(array $data)
  {
    $this->data = $data;
    $this->dimensions = count($data);
  }

  public function getDimensionValue(int $i): float
  {
    return $this->data[$i];
  }

  public function getDimensions(): int
  {
    return $this->dimensions;
  }

  public function dot(IVector $other): float
  {
    if ($this->dimensions != $other->getDimensions()) {
      throw new IncompatibleDimensionsException('Vectors must have the same dimensions');
    }

    $result = 0;

    for ($i = 0; $i < $this->dimensions; $i++) {
      $result += $this->getDimensionValue($i) * $other->getDimensionValue($i);
    }

    return $result;
  }

  public function norm(): float
  {
    $sum = 0;
    foreach ($this->data as $value) {
      $sum += pow($value, 2);
    }
    return sqrt($sum);
  }
}

class IncompatibleDimensionsException extends \Exception
{
}