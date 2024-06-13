<?php


use PHPUnit\Framework\TestCase;
use App\Services\Recommendation\Similarity\CosineSimilarity;
use App\Services\Recommendation\Utils\IVector;

// Dummy implementation of IVector for testing purposes
class DummyVector implements IVector
{
  private $vector;

  public function __construct(array $vector)
  {
    $this->vector = $vector;
  }

  public function getDimensionValue(int $id): float
  {
    return $this->vector[$id];
  }

  public function getDimensions(): int
  {
    return sizeof($this->vector);
  }

  public function dot(IVector $other): float
  {
    $result = 0.0;
    foreach ($this->vector as $index => $value) {
      $result += $value * $other->getDimensionValue($index);
    }
    return $result;
  }

  public function norm(): float
  {
    return sqrt(array_sum(array_map(function ($x) {
      return $x * $x;
    }, $this->vector)));
  }

  public function get($index)
  {
    return $this->vector[$index];
  }

  public static function fromArr(array $data): DummyVector
  {
    return new DummyVector($data);
  }

  public function intersect(IVector $other): DummyVector
  {
    return new DummyVector($this->vector);
  }
}


class CosineSimilarityTest extends TestCase
{
  public function testCalculateReturnsCorrectSimilarity()
  {
    // Create mock vectors
    $firstVector = $this->createMock(IVector::class);
    $secondVector = $this->createMock(IVector::class);

    // Set up expectations for the mock vectors
    $firstVector->expects($this->once())
      ->method('dot')
      ->with($secondVector)
      ->willReturn(10.0);

    $firstVector->expects($this->once())
      ->method('norm')
      ->willReturn(5.0);

    $secondVector->expects($this->once())
      ->method('norm')
      ->willReturn(3.0);

    // Create an instance of the CosineSimilarity class
    $cosineSimilarity = new CosineSimilarity();

    // Calculate the similarity
    $similarity = $cosineSimilarity->calculate($firstVector, $secondVector);

    // Assert that the calculated similarity is correct
    $this->assertEquals(10.0 / 15, $similarity);
  }


  public function testCalculate()
  {
    // Example vectors
    $vector1 = new DummyVector([1, 2, 3]);
    $vector2 = new DummyVector([4, 5, 6]);

    $cosineSimilarity = new CosineSimilarity();
    $similarity = $cosineSimilarity->calculate($vector1, $vector2);

    // Assert that the similarity result is within a small margin of error, e.g., due to float precision
    $this->assertEqualsWithDelta(0.974631846, $similarity, 0.00001, '');
  }
}