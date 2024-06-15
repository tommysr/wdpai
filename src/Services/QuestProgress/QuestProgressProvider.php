<?php

namespace App\Services\QuestProgress;

use App\Models\Interfaces\IQuestProgress;
use App\Models\QuestState;
use App\Repository\QuestProgress\IQuestProgressRepository;
use App\Services\Quests\IQuestProvider;
use App\Services\Session\ISessionService;

class QuestProgressProvider implements IQuestProgressProvider
{
  private IQuestProgressRepository $questProgressRepository;
  private ISessionService $sessionService;
  private IQuestProvider $questProvider;

  public function __construct(ISessionService $sessionService, IQuestProgressRepository $questProgressRepository, IQuestProvider $questProvider)
  {
    $this->sessionService = $sessionService;
    $this->questProgressRepository = $questProgressRepository;
    $this->questProvider = $questProvider;
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

    $quest = $this->questProvider->getQuest($questId);
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

  public function getCompletedWallets(int $questId): array
  {
    $progresses = $this->questProgressRepository->getAllProgresses($questId);
    $completed = array_filter($progresses, fn($progress) => $progress->getState() === QuestState::Rated);
    usort($completed, fn($a, $b) => $a->getScore() <=> $b->getScore());
    return array_map(fn($progress) => $progress->getWalletAddress(), $completed);
  }

  public function getUserQuests(int $userId): array
  {
    $progresses = $this->questProgressRepository->getUserEntries($userId);

    $stats = array_map(fn($progress) => [
      'quest' => $this->questProvider->getQuest($progress->getQuestId()),
      'progress' => $progress
    ], $progresses);

    return $stats;
  }
}