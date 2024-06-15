<?php

use PHPUnit\Framework\TestCase;
use App\Services\Recommendation\Data\DataManager;

class DataManagerTest extends TestCase
{
  public function testSetData()
  {
    $data = ['Data 1', 'Data 2'];
    $manager = new DataManager();

    $manager->setData($data);

    $this->assertEquals($data, $manager->getData());
  }

  public function testGetSimilarityMatrix()
  {
    $similarityMatrix = ['Matrix 1', 'Matrix 2'];
    $manager = new DataManager([], $similarityMatrix);

    $this->assertEquals($similarityMatrix, $manager->getSimilarityMatrix());
  }
}