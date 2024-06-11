<?php

namespace App\Services\Rating;

use App\Models\Interfaces\IRating;
use App\Repository\IQuestRepository;
use App\Repository\IUserRepository;
use App\Repository\QuestRepository;
use App\Repository\Rating\RatingRepository;
use App\Repository\UserRepository;
use App\Services\Rating\IRatingService;
use App\Repository\Rating\IRatingRepository;


class RatingService implements IRatingService
{
  private IRatingRepository $ratingRepository;
  private IQuestRepository $questRepository;
  private IUserRepository $userRepository;

  public function __construct(IRatingRepository $ratingRepository, IQuestRepository $questRepository, IUserRepository $userRepository)
  {
    $this->ratingRepository = $ratingRepository;
    $this->questRepository = $questRepository;
    $this->userRepository = $userRepository;
  }

  public function addRating(IRating $rating): void
  {
    $this->ratingRepository->addRating($rating);
  }

  public function getUserItemMatrix(): array
  {
    $ratings = $this->ratingRepository->getRatings();
    $maxUserId = $this->userRepository->getMaxUserId();
    $maxQuestId = $this->questRepository->getMaxQUestId();


    for ($i = 0; $i <= $maxUserId; $i++) {
      for ($j = 0; $j <= $maxQuestId; $j++) {
        $userItemMatrix[$i][$j] = 0;
      }
    }

    foreach ($ratings as $rating) {
      $userId = $rating->getUserId();
      $questId = $rating->getQuestId();
      $userItemMatrix[$userId][$questId] = $rating->getRating();
    }


    return $userItemMatrix;
  }
}