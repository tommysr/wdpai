<?php

namespace App\Services\Recommendation\Calculator;

use App\Services\Recommendation\Calculator\ISimilarityCalculator;
use App\Services\Recommendation\Similarity\ISimilarityStrategy;
use App\Services\Recommendation\Utils\Vector;

class SimilarityCalculator implements ISimilarityCalculator
{
  protected array $data;
  protected ISimilarityStrategy $similarityStrategy;

  public function __construct(array $data, ISimilarityStrategy $similarityStrategy)
  {
    $this->data = $data;
   
    $this->similarityStrategy = $similarityStrategy;
  }

  public function calculate(): array
  {
    $userCount = count($this->data);
    $similarityMatrix = [];

    for ($i = 0; $i < $userCount; $i++) {
      for ($j = 0; $j < $userCount; $j++) {
        if ($i == $j) {
          $similarityMatrix[$i][$j] = 1.0;
        } else {

          $vector1 = new Vector($this->data[$i]);
          $vector2 = new Vector($this->data[$j]);


          $similarityMatrix[$i][$j] = $this->similarityStrategy->calculate($vector1->intersect($vector2), $vector2->intersect($vector1));
        }
      }
    }
    
    return $similarityMatrix;
  }
}