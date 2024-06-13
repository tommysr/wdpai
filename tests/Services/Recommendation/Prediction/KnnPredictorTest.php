<?php


use App\Services\Recommendation\Data\DataManager;
use PHPUnit\Framework\TestCase;
use App\Services\Recommendation\Prediction\KnnPredictor;
use App\Services\Recommendation\Data\IDataManager;


class KnnPredictorTest extends TestCase
{
  public function testPredict()
  {
    // Example data and similarity matrix
    $data = [
      [0, 0, 0, 0, 0],
      [0, 5, 3, 0, 1], // User 0
      [0, 4, 0, 5, 1], // User 1
      [0, 1, 1, 0, 5], // User 2
      [0, 5, 3, 5, 1], // User 3
      [0, 5, 3, 2, 1], // User 4
    ];


    $similarityMatrix = [
      [1, 0, 0, 0, 0, 0],
      [0, 1, 0.54772255750517, 0.42289003161103, 0.76376261582597, 0.94733093343134],
      [0, 0.54772255750517, 1, 0.26726124191242, 0.91634193382304, 0.76595762721647],
      [0, 0.42289003161103, 0.26726124191242, 1, 0.32298759674997, 0.40061680838489],
      [0, 0.76376261582597, 0.91634193382304, 0.32298759674997, 1, 0.93026050941906],
      [0, 0.94733093343134, 0.76595762721647, 0.40061680838489, 0.93026050941906, 1],
    ];


    $dataManager = new DataManager($data, $similarityMatrix);
    $knnPredictor = new KnnPredictor($dataManager, 6);

    // Test prediction for userId = 0, itemId = 3
    $prediction = $knnPredictor->predict(1, 3);

    // Assert that the prediction result matches expected value within a small delta
    $this->assertEqualsWithDelta(3.74, $prediction, 0.01, '');
  }
}