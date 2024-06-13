<?php

namespace App\Services\Recommendation\Utils;

use App\Services\Recommendation\Utils\IVector;

class Vector implements IVector
{
  private array $data;
  private int $dimensions;

  public function __construct(array $data)
  {
    $this->data = $data;
    $this->dimensions = count($data);
  }

  public static function fromArr(array $data): IVector
  {
    return new Vector($data);
  }

  public function intersect(IVector $other): IVector
  {
    $res = array_fill(0, $this->dimensions, 0);

    if ($this->dimensions !== $other->getDimensions()) {
      throw new \InvalidArgumentException('Vectors must be of the same length.');
    }

    for ($i = 0; $i < $this->dimensions; $i++) {
      if ($other->getDimensionValue($i) === 0.0) {
        $res[$i] = 0;
      } else {
        $res[$i] = $this->data[$i];
      }
    }

    return new Vector($res);
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