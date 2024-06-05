<?php

namespace App\Services\Recommendation;

use App\Repository\Rating\IRatingRepository;
use App\Repository\Rating\RatingRepository;
use App\Repository\Similarity\ISimilarityRepository;
use App\Repository\Similarity\SimilarityRepository;
use App\Services\Rating\IRatingService;
use App\Services\Rating\RatingService;
use App\Services\Recommendation\Data\DataManager;
use App\Services\Recommendation\Data\IDataManager;
use App\Services\Recommendation\IRecommendationService;
use App\Services\Recommendation\Prediction\KnnPredictor;
use App\Services\Recommendation\Recommender\IRecommender;
use App\Services\Recommendation\Recommender\Recommender;
use App\Services\Recommendation\Similarity\CosineSimilarity;


class RecommendationService implements IRecommendationService
{
  private IRatingService $ratingService;
  private ISimilarityRepository $similarityRepository;
  private IRecommender $recommender;
  private IDataManager $dataManager;

  public function __construct(IRatingService $ratingService = null, ISimilarityRepository $similarityRepository = null, IRecommender $recommender = null, IDataManager $dataManager = null)
  {
    $this->ratingService = $ratingService ?: new RatingService();
    $this->similarityRepository = $similarityRepository ?: new SimilarityRepository();

    $data = $this->ratingService->getUserItemMatrix();
    $similarityMatrix = $this->similarityRepository->getSimilarityMatrix();
    $this->dataManager = new DataManager($data, $similarityMatrix);
    $this->recommender = $recommender ?: new Recommender($this->dataManager);

    // TODO: replace it with something else, e.g DI
    $this->recommender->setSimilarityStrategy(new CosineSimilarity());
    $this->recommender->setPredictionStrategy(new KnnPredictor($data, $similarityMatrix));
  }


  public function getRecommendations(int $userId): array
  {
    $predicted=  $this->recommender->estimate($userId);
    $sortedRecommendations = array_filter($predicted, function($value, $key) {
      return $value !== 0.0 && $key !== 0;
    }, ARRAY_FILTER_USE_BOTH);
    arsort($sortedRecommendations);
    $sortedRecommendations = array_keys($sortedRecommendations);

    return $sortedRecommendations;
  }

  private function saveSimilarities(): void
  {
    $newSimilarities = $this->dataManager->getSimilarityMatrix();
    $this->similarityRepository->deleteSimilarityMatrix();
    $this->similarityRepository->saveSimilarityMatrix($newSimilarities);
  }

  public function refreshRecommendations(): void
  {
    $data = $this->ratingService->getUserItemMatrix();
    $this->dataManager->setData($data);
    $this->recommender->construct();

    $this->saveSimilarities();
  }
}