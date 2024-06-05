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

  public function __construct(IRatingRepository $ratingRepository = null, IQuestRepository $questRepository = null, IUserRepository $userRepository = null)
  {
    $this->ratingRepository = $ratingRepository ?: new RatingRepository();
    $this->questRepository = $questRepository ?: new QuestRepository();
    $this->userRepository = $userRepository ?: new UserRepository();
  }

  public function addRating(IRating $rating): void
  {
    $this->ratingRepository->addRating($rating);
  }

  public function getUserItemMatrix(): array
  {
    $ratings = $this->ratingRepository->getRatings();
    $users = $this->userRepository->getAllUserIds();
    $quests = $this->questRepository->getAllQuestIds();

    $userItemMatrix = [];
    foreach ($users as $userId) {
      foreach ($quests as $questId) {
        $userItemMatrix[$userId][$questId] = 0;
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