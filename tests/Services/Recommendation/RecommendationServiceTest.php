<?php

use PHPUnit\Framework\TestCase;
use App\Services\Recommendation\RecommendationService;
use App\Services\Rating\IRatingService;
use App\Repository\Similarity\ISimilarityRepository;
use App\Services\Recommendation\Data\IDataManager;
use App\Services\Recommendation\Recommender\IRecommender;
use function PHPUnit\Framework\assertTrue;

class RecommendationServiceTest extends TestCase
{
  public function testGetRecommendations()
  {
    // Create mock objects for dependencies
    $ratingService = $this->createMock(IRatingService::class);
    $similarityRepository = $this->createMock(ISimilarityRepository::class);
    $recommender = $this->createMock(IRecommender::class);
    $dataManager = $this->createMock(IDataManager::class);

    // Set up expectations for the mock objects
    $ratingService->expects($this->once())
      ->method('getUserItemMatrix')
      ->willReturn([
        [1, 2, 3],
        [4, 5, 6],
        [7, 8, 9]
      ]);

    $similarityRepository->expects($this->once())
      ->method('getSimilarityMatrix')
      ->willReturn([
        [0.5, 0.2, 0.8],
        [0.3, 0.6, 0.1],
        [0.9, 0.4, 0.7]
      ]);

    $recommender->expects($this->once())
      ->method('estimate')
      ->with(1)
      ->willReturn([
        2 => 0.9,
        3 => 0.6,
        1 => 0.8
      ]);

    // Create an instance of the RecommendationService class
    $recommendationService = new RecommendationService($ratingService, $similarityRepository, $recommender, $dataManager);

    // Call the method being tested
    $recommendations = $recommendationService->getRecommendations(1);

    // Assert the expected result
    $this->assertEquals([2, 1, 3], $recommendations);
  }


  public function testRefreshRecommendations()
  {
    // Create mock objects for dependencies
    $ratingService = $this->createMock(IRatingService::class);
    $similarityRepository = $this->createMock(ISimilarityRepository::class);
    $recommender = $this->createMock(IRecommender::class);
    $dataManager = $this->createMock(IDataManager::class);

    // Set up expectations for the mock objects
    $ratingService->expects($this->exactly(2))
      ->method('getUserItemMatrix')
      ->willReturn([
        [1, 2, 3],
        [4, 5, 6],
        [7, 8, 9]
      ]);

    $dataManager->expects($this->exactly(2))
      ->method('setData')
      ->with([
        [1, 2, 3],
        [4, 5, 6],
        [7, 8, 9]
      ]);

    $recommender->expects($this->once())
      ->method('construct');

    $dataManager->expects($this->once())
      ->method('getSimilarityMatrix')
      ->willReturn([
        [0.5, 0.2, 0.8],
        [0.3, 0.6, 0.1],
        [0.9, 0.4, 0.7]
      ]);

    $similarityRepository->expects($this->once())
      ->method('deleteSimilarityMatrix');

    $similarityRepository->expects($this->once())
      ->method('saveSimilarityMatrix')
      ->with([
        [0.5, 0.2, 0.8],
        [0.3, 0.6, 0.1],
        [0.9, 0.4, 0.7]
      ]);

    // Create an instance of the RecommendationService class
    $recommendationService = new RecommendationService($ratingService, $similarityRepository, $recommender, $dataManager);

    // Call the method being tested
    $recommendationService->refreshRecommendations();

    $this->assertTrue(true); // Or any other simple assertion

  }
}