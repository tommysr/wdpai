<?php

use App\Services\Recommendation\Data\DataManager;
use PHPUnit\Framework\TestCase;
use App\Services\Recommendation\Recommender\Recommender;
use App\Services\Recommendation\Data\IDataManager;
use App\Services\Recommendation\Similarity\CosineSimilarity;
use App\Services\Recommendation\Prediction\KnnPredictor;



class RecommenderTest extends TestCase
{
  public function testRecommendations()
  {
    // Example data
    $data = [
      [0, 0, 0, 0, 0],
      [0, 5, 3, 0, 1], // User 0
      [0, 4, 0, 5, 1], // User 1
      [0, 1, 1, 0, 5], // User 2
      [0, 5, 3, 5, 1], // User 3
      [0, 5, 3, 2, 1], // User 4
    ];

    // Create a dummy data manager
    $dataManager = new DataManager($data);

    // Create a recommender instance
    $recommender = new Recommender($dataManager);

    // Set similarity strategy (e.g., Cosine similarity)
    $cosineSimilarity = new CosineSimilarity();
    $recommender->setSimilarityStrategy($cosineSimilarity);

    // Calculate similarity matrix
    $recommender->calculateSimilarityMatrix();

    // print_r($recommender->getSimilarityMatrix());

    // Set prediction strategy (e.g., KNN predictor)
    $knnPredictor = new KnnPredictor($dataManager, 6);
    $recommender->setPredictionStrategy($knnPredictor);

    // Estimate recommendations for user with index 0
    $recommendations = $recommender->estimate(1);

    // Assert the recommendations for user 0 and item 3
    $this->assertEqualsWithDelta(3.99, $recommendations[3], 0.01, '');

    // 3.74 without intersecting
  }
}
