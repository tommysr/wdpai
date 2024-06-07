<?php

use PHPUnit\Framework\TestCase;
use App\Services\Recommendation\Recommender;
use App\Services\Recommendation\CosineSimilarity;


function getTopNRecommendations($predictedRatings, $n = 3)
{
  arsort($predictedRatings);
  return array_slice(array_keys($predictedRatings), 0, $n, true);
}

class RecommendationTest extends TestCase
{
  // Test case for computing similarity matrix
  public function testComputeSimilarityMatrix()
  {
    //   // // Test data
    //   // $yr = [
    //   //   0 => [[0, 3], [1, 3], [2, 3], [5, 1], [6, 1.5], [7, 3]],
    //   //   1 => [
    //   //     [0, 4],
    //   //     [1, 4],
    //   //     [2, 4],
    //   //   ]
    //   // ];
    //   // $n_x = 2;
    //   // $min_support = 1;

    //   // // Shuffle ratings in yr array
    //   // foreach ($yr as &$ratings) {
    //   //   shuffle($ratings);
    //   // }
    //   // unset($ratings);

    //   $matrix = [
    //     [5, 3, 0, 1], // User 0
    //     [4, 0, 5, 1], // User 1
    //     [1, 1, 0, 5], // User 2
    //     [5, 3, 5, 1], // User 3
    //     [5, 3, 2, 1], // User 4
    //   ];

    //   // Call the function to compute the similarity matrix
    //   $similarity = new CosineSimilarity();
    //   $cosineRecommender = new Recommender($matrix, $similarity);
    //   $cosineRecommender->construct();


    //   $predictedRatings = $cosineRecommender->estimate(0);

    //   print_r($predictedRatings);
  }
}
