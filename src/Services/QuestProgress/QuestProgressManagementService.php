<?php

namespace App\Services\QuestProgress;

use App\Models\Interfaces\IQuestProgress;
use App\Models\IQuestion;
use App\Models\QuestionType;
use App\Models\QuestProgress;
use App\Models\QuestState;
use App\Repository\IOptionsRepository;
use App\Repository\IQuestionsRepository;
use App\Repository\IWalletRepository;
use App\Repository\OptionsRepository;
use App\Repository\QuestionsRepository;
use App\Repository\QuestProgress\IQuestProgressRepository;
use App\Repository\QuestProgress\QuestProgressRepository;
use App\Repository\WalletRepository;
use App\Services\Question\IQuestionService;
use App\Services\QuestProgress\IQuestProgressService;
use App\Services\Quests\IQuestService;
use App\Services\Quests\QuestService;
use App\Services\Session\ISessionService;

class QuestProgressManagementService implements IQuestProgressManagementService
{
  private IQuestProgressRepository $questProgressRepository;
  private ISessionService $sessionService;
  private IQuestionsRepository $questionsRepository;
  private IQuestService $questService;
  private IQuestProgressRetrievalService $questProgressRetrieval;
  private IQuestionService $questionService;

  public function __construct(ISessionService $sessionService, IQuestProgressRepository $questProgressRepository, IQuestionsRepository $questionsRepository, IQuestService $questService, IQuestProgressRetrievalService $questProgressRetrieval)
  {
    $this->sessionService = $sessionService;
    $this->questProgressRepository = $questProgressRepository;
    $this->questionsRepository = $questionsRepository;
    $this->questService = $questService;
    $this->questProgressRetrieval = $questProgressRetrieval;
  }

  public function resetSession(): void
  {
    $this->sessionService->delete('questProgress');
  }

  public function startProgress(int $questId, int $walletId): void
  {
    $quest = $this->questService->getQuest($questId);

    if (!$quest) {
      throw new \Exception("Quest not found");
    }

    if (!$this->questService->addParticipant($questId)) {
      throw new \Exception("Failed to add participant to quest");
    }

    $nextQuestionId = $this->questionsRepository->getNextQuestion($questId, 0)->getQuestionId();
    $questProgress = new QuestProgress(null, 0, $questId, $walletId, $nextQuestionId, QuestState::InProgress, '');

    $this->questProgressRepository->saveQuestProgress($questProgress);
    $this->sessionService->set('questProgress', $questProgress);
  }

  public function completeQuest(): void
  {
    $questProgress = $this->questProgressRetrieval->getCurrentProgress();
    $questProgress->setState(QuestState::Finished);
    $questProgress->setCompletionDateToNow();
    $this->sessionService->set('questProgress', $questProgress);
    $this->questProgressRepository->updateQuestProgress($questProgress);
  }

  public function addPoints(int $pointsGained): void
  {
    $questProgress = $this->questProgressRetrieval->getCurrentProgress();
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
    $questProgress = $this->questProgressRetrieval->getCurrentProgress();
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
    $questProgress = $this->questProgressRetrieval->getCurrentProgress();
    $questProgress->setState(QuestState::Abandoned);
    $this->sessionService->delete('questProgress');
    $this->questProgressRepository->updateQuestProgress($questProgress);
  }
}