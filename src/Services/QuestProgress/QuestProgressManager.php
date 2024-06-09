<?php

namespace App\Services\QuestProgress;

use App\Models\QuestProgress;
use App\Models\QuestState;
use App\Repository\IQuestionsRepository;
use App\Repository\QuestProgress\IQuestProgressRepository;
use App\Services\Quest\IQuestProvider;
use App\Services\Quests\IQuestManager;
use App\Services\Session\ISessionService;

class QuestProgressManager implements IQuestProgressManager
{
  private IQuestProgressRepository $questProgressRepository;
  private ISessionService $sessionService;
  private IQuestionsRepository $questionsRepository;
  private IQuestProvider $questProvider;
  private IQuestManager $questManager;
  private IQuestProgressProvider $questProgressProvider;

  public function __construct(ISessionService $sessionService, IQuestProgressRepository $questProgressRepository, IQuestionsRepository $questionsRepository, IQuestProvider $questProvider, IQuestManager $questManager, IQuestProgressProvider $questProgressProvider)
  {
    $this->sessionService = $sessionService;
    $this->questProgressRepository = $questProgressRepository;
    $this->questionsRepository = $questionsRepository;
    $this->questProvider = $questProvider;
    $this->questManager = $questManager;
    $this->questProgressProvider = $questProgressProvider;
  }

  public function resetSession(): void
  {
    $this->sessionService->delete('questProgress');
  }

  public function startProgress(int $questId, int $walletId): void
  {
    $quest = $this->questProvider->getQuest($questId);

    if (!$quest) {
      throw new \Exception("Quest not found");
    }

    if (!$this->questManager->addParticipant($questId)) {
      throw new \Exception("Failed to add participant to quest");
    }

    $nextQuestionId = $this->questionsRepository->getNextQuestion($questId, 0)->getQuestionId();
    $questProgress = new QuestProgress(null, 0, $questId, $walletId, $nextQuestionId, QuestState::InProgress, '');

    $this->questProgressRepository->saveQuestProgress($questProgress);
    $this->sessionService->set('questProgress', $questProgress);
  }

  public function completeQuest(): void
  {
    $questProgress = $this->questProgressProvider->getCurrentProgress();

    $questProgress->setState(QuestState::Finished);
    $questProgress->setCompletionDateToNow();
    $this->sessionService->set('questProgress', $questProgress);
    $this->questProgressRepository->updateQuestProgress($questProgress);
  }

  public function addPoints(int $pointsGained): void
  {
    $questProgress = $this->questProgressProvider->getCurrentProgress();
    $questProgress->setScore($questProgress->getScore() + $pointsGained);
    $this->sessionService->set('questProgress', $questProgress);
    $this->questProgressRepository->updateQuestProgress($questProgress);
  }

  public function recordResponses(int $userId, array $selectedOptionsIds): void
  {
    $this->questProgressRepository->saveResponses($userId, $selectedOptionsIds);
  }

  public function changeProgress(int $questionId): void
  {
    $questProgress = $this->questProgressProvider->getCurrentProgress();
    $questionId = $this->questionsRepository->getNextQuestionId($questProgress->getQuestId(), $questProgress->getLastQuestionId());

    if (!$questionId) {
      $questProgress->setState(QuestState::Unrated);
    } else {
      $questProgress->setNextQuestionId($questionId);
    }

    $this->sessionService->set('questProgress', $questProgress);
    $this->questProgressRepository->updateQuestProgress($questProgress);
  }

  public function abandonQuest(): void
  {
    $questProgress = $this->questProgressProvider->getCurrentProgress();
    $questProgress->setState(QuestState::Abandoned);
    $this->sessionService->delete('questProgress');
    $this->questProgressRepository->updateQuestProgress($questProgress);
  }
}