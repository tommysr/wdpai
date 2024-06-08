<?php

namespace App\Services\QuestProgress;

use App\Models\Interfaces\IQuestProgress;
use App\Repository\IOptionsRepository;
use App\Repository\IQuestionsRepository;
use App\Repository\IWalletRepository;
use App\Repository\QuestProgress\IQuestProgressRepository;
use App\Services\Quests\IQuestService;
use App\Services\Session\ISessionService;

class QuestProgressRetrievalService implements IQuestProgressRetrievalService
{
  private IQuestProgressRepository $questProgressRepository;
  private ISessionService $sessionService;
  private IQuestService $questService;

  public function __construct(ISessionService $sessionService, IQuestProgressRepository $questProgressRepository, IQuestService $questService)
  {
    $this->sessionService = $sessionService;
    $this->questProgressRepository = $questProgressRepository;
    $this->questService = $questService;
  }

  public function isQuestPlayed(int $userId, int $questId): bool
  {
    return $this->questProgressRepository->getQuestProgress($userId, $questId) !== null;
  }

  public function getResponsesCount(int $optionId): int
  {
    return $this->questProgressRepository->getResponsesCount($optionId);
  }

  public function getQuestSummary(int $userId, int $questId): array
  {
    $questProgress = $this->getProgress($userId, $questId);

    if (!$questProgress) {
      return [];
    }

    $quest = $this->questService->getQuest($questId);
    $percentileRank = $this->questProgressRepository->getPercentileRank($userId, $questId);

    return [
      'score' => $questProgress->getScore(),
      'maxScore' => $quest->getMaxPoints(),
      'better_than' => $percentileRank,
    ];
  }

  public function getProgress(int $userId, int $questId): ?IQuestProgress
  {
    $sessionProgress = $this->sessionService->get('questProgress');
    return $sessionProgress ?? $this->questProgressRepository->getQuestProgress($userId, $questId);
  }

  public function getCurrentProgress(): ?IQuestProgress
  {
    return $this->sessionService->get('questProgress');
  }

  public function getUserQuests(int $userId): array
  {
    $progresses = $this->questProgressRepository->getUserEntries($userId);

    $stats = array_map(fn($progress) => [
      'quest' => $this->questService->getQuest($progress->getQuestId()),
      'progress' => $progress
    ], $progresses);

    return $stats;
  }
}