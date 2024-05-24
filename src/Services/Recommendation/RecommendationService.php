<?php

namespace App\Services\Recommendation;

use App\Repository\Rating\IRatingRepository;
use App\Repository\Rating\RatingRepository;
use App\Repository\Similarity\ISimilarityRepository;
use App\Repository\Similarity\SimilarityRepository;
use App\Services\Rating\IRatingService;
use App\Services\Rating\RatingService;
use App\Services\Recommendation\IRecommendationService;


class RecommendationService implements IRecommendationService
{
  private IRatingService $ratingService;
  private ISimilarityRepository $similarityRepository;
  private IRecommender $recommender;

  public function __construct(IRatingService $ratingService = null, ISimilarityRepository $similarityRepository = null, IRecommender $recommender = null)
  {
    $this->ratingService = $ratingService ?: new RatingService();
    $this->similarityRepository = $similarityRepository ?: new SimilarityRepository();

    if ($recommender) {
      $this->recommender = $recommender;
      return;
    }

    $builder = new RecommenderBuilder();
    $builder->setData($this->ratingService->getUserItemMatrix());
    $builder->setSimilarityMatrix($this->similarityRepository->getSimilarityMatrix());
    $builder->setPredictionStrategy('knn', ['k' => 5]);
    $builder->setSimilarityStrategy('cosine', []);

    $this->recommender = $recommender ?: $builder->build();
  }

  public function getRecommendations(int $userId): array
  {
    $data = $this->ratingService->getUserItemMatrix();
    $similarityMatrix = $this->similarityRepository->getSimilarityMatrix();

    $this->recommender->setData($data);
    $this->recommender->setSimilarityMatrix($similarityMatrix);
    $this->recommender->construct();

    return $this->recommender->estimate($userId);
  }

  public function getPopularItems(): array
  {
    return $this->recommender->getSimilarityMatrix();
  }

}