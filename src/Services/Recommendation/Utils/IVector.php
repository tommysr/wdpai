<?php

namespace App\Services\Recommendation\Utils;

interface IVector
{
  public static function fromArr(array $data): IVector;
  public function norm(): float;
  public function dot(IVector $other): float;
  public function getDimensionValue(int $i): float;
  public function getDimensions(): int;
  public function intersect(IVector $other): IVector;
}