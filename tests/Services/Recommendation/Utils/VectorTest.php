<?php

use App\Services\Recommendation\Utils\IncompatibleDimensionsException;
use PHPUnit\Framework\TestCase;
use App\Services\Recommendation\Utils\Vector;
use App\Services\Recommendation\Utils\IVector;

class VectorTest extends TestCase
{
  public function testFromArr()
  {
    $data = [1, 2, 3];
    $vector = Vector::fromArr($data);

    $this->assertInstanceOf(IVector::class, $vector);
    $this->assertEquals($data, [$vector->getDimensionValue(0), $vector->getDimensionValue(1), $vector->getDimensionValue(2)]);
  }

  public function testIntersect()
  {
    $data1 = [1, 2, 3];
    $data2 = [0, 2, 0];
    $expectedResult = [0, 2, 0];

    $vector1 = new Vector($data1);
    $vector2 = new Vector($data2);
    $result = $vector1->intersect($vector2);

    $this->assertInstanceOf(IVector::class, $result);
    $this->assertEquals($expectedResult, [$result->getDimensionValue(0), $result->getDimensionValue(1), $result->getDimensionValue(2)]);
  }

  public function testIntersectWithDifferentDimensions()
  {
    $data1 = [1, 2, 3];
    $data2 = [0, 2];
    $vector1 = new Vector($data1);
    $vector2 = new Vector($data2);

    $this->expectException(\InvalidArgumentException::class);
    $vector1->intersect($vector2);
  }

  public function testDot()
  {
    $data1 = [1, 2, 3];
    $data2 = [4, 5, 6];
    $expectedResult = 32;

    $vector1 = new Vector($data1);
    $vector2 = new Vector($data2);
    $result = $vector1->dot($vector2);

    $this->assertEquals($expectedResult, $result);
  }

  public function testDotWithDifferentDimensions()
  {
    $data1 = [1, 2, 3];
    $data2 = [4, 5];
    $vector1 = new Vector($data1);
    $vector2 = new Vector($data2);

    $this->expectException(IncompatibleDimensionsException::class);
    $vector1->dot($vector2);
  }

  public function testNorm()
  {
    $data = [3, 4];
    $expectedResult = 5;

    $vector = new Vector($data);
    $result = $vector->norm();

    $this->assertEquals($expectedResult, $result);
  }
}