<?php

namespace App\Services\QuestProgress;

use App\Models\Interfaces\IQuestProgress;
use App\Models\IQuestion;
use App\Models\QuestionType;
use App\Models\QuestProgress;
use App\Models\QuestState;
use App\Repository\IOptionsRepository;
use App\Repository\IQuestionsRepository;
use App\Repository\OptionsRepository;
use App\Repository\QuestionsRepository;
use App\Repository\QuestProgress\IQuestProgressRepository;
use App\Repository\QuestProgress\QuestProgressRepository;
use App\Services\QuestProgress\IQuestProgressService;
use App\Services\Quests\IQuestService;
use App\Services\Quests\QuestService;
use App\Services\Session\ISessionService;

class QuestProgressService implements IQuestProgressService
{
  private IQuestProgressRepository $questProgressRepository;
  private ISessionService $sessionService;
  private IQuestionsRepository $questionsRepository;
  private IOptionsRepository $optionsRepository;
  private IQuestService $questService;

  public function __construct(ISessionService $sessionService, IQuestProgressRepository $questProgressRepository = null, IQuestionsRepository $questionsRepository = null, IQuestService $questService = null, IOptionsRepository $optionsRepository = null)
  {
    $this->sessionService = $sessionService;
    $this->questProgressRepository = $questProgressRepository ?: new QuestProgressRepository();
    $this->questionsRepository = $questionsRepository ?: new QuestionsRepository();
    $this->optionsRepository = $optionsRepository ?: new OptionsRepository();
    $this->questService = $questService ?: new QuestService();
  }

  public function isQuestPlayed(int $userId, int $questId): bool
  {
    return $this->questProgressRepository->getQuestProgress($userId, $questId) !== null;
  }

  public function getQuestSummary(int $userId): array
  {
    $questProgress = $this->getCurrentProgressFromSession();
    $questId = $questProgress->getQuestId();
    $quest = $this->questService->getQuest($questId);
    $percentileRank = $this->questProgressRepository->getPercentileRank($userId, $questId);

    return [
      'score' => $questProgress->getScore(),
      'maxScore' => $quest->getMaxPoints(),
      'better_than' => $percentileRank,
    ];
  }

  public function getCurrentProgress(int $userId, int $questId): IQuestProgress
  {
    $sessionProgress = $this->sessionService->get('questProgress');
    $progress = $sessionProgress ?? $this->questProgressRepository->getQuestProgress($userId, $questId);

    if (!$progress) {
      throw new \Exception("Quest progress not found");
    }

    return $progress;
  }

  public function getCurrentProgressFromSession(): IQuestProgress
  {
    $progress = $this->sessionService->get('questProgress');

    if (!$progress) {
      throw new \Exception("Quest progress not found");
    }

    return $progress;
  }

  public function getQuestion(int $questId, int $questionId): ?IQuestion
  {
    $question = $this->questionsRepository->getById($questionId);

    if (!$question) {
      return null;
    }

    $options = $this->optionsRepository->getOptionsByQuestionId($questionId);
    $question->setOptions($options);
    return $question;
  }

  public function getUserQuests(int $userId): array
  {
    $progresses =  $this->questProgressRepository->getUserEntries($userId);

    return array_map(fn($progress) => [
      'quest' => $this->questService->getQuest($progress->getQuestId()),
      'progress' => $progress
    ], $progresses);
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
    $questProgress = new QuestProgress(null, 0, $questId, $walletId, $nextQuestionId, QuestState::InProgress);

    $this->questProgressRepository->saveQuestProgress($questProgress);
    $this->sessionService->set('questProgress', $questProgress);
  }

  public function evaluateOptions(int $questionId, array $selectedOptions): array
  {
    $question = $this->questionsRepository->getById($questionId);

    if ($question->getType() === QuestionType::READ_TEXT) {
      return ['points' => $question->getPoints(), 'options' => [], 'maxPoints' => $question->getPoints()];
    }

    $options = $this->optionsRepository->getOptionsByQuestionId($questionId);
    $optionIds = array_map(fn($option) => $option->getOptionId(), $options);
    $correctIds = $this->optionsRepository->getCorrectOptionsIdsForQuestionId($questionId);
    $correctCount = count($correctIds);
    $chosenCount = count(array_intersect($correctIds, $selectedOptions));

    if ($question->getType() === QuestionType::SINGLE_CHOICE && $chosenCount != 1) {
      throw new \Exception("Single choice question must have exactly one option selected");
    }

    if ($question->getType() === QuestionType::MULTIPLE_CHOICE && $chosenCount < 1) {
      throw new \Exception("Multiple choice question must have at least one option selected");
    }

    $points = round(($chosenCount / $correctCount) * $question->getPoints());

    return [
      'points' => $points,
      'maxPoints' => $question->getPoints(),
      'options' => array_intersect($correctIds, $optionIds)
    ];
  }

  public function completeQuest(): void
  {
    $questProgress = $this->getCurrentProgressFromSession();
    $questProgress->setState(QuestState::Finished);
    $questProgress->setCompletionDateToNow();
    $this->sessionService->set('questProgress', $questProgress);
    $this->questProgressRepository->updateQuestProgress($questProgress);
  }

  public function updateQuestProgress(int $pointsGained): void
  {
    $questProgress = $this->getCurrentProgressFromSession();
    $questProgress->setScore($questProgress->getScore() + $pointsGained);
    $this->sessionService->set('questProgress', $questProgress);
    $this->questProgressRepository->updateQuestProgress($questProgress);
  }

  public function recordResponses(int $userId, array $selectedOptionsIds): void
  {
    $this->questProgressRepository->saveResponses($userId, $selectedOptionsIds);
  }

  public function adjustQuestProgress(int $questionId): void
  {
    $questProgress = $this->getCurrentProgressFromSession();
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
    $questProgress = $this->getCurrentProgressFromSession();
    $questProgress->setState(QuestState::Abandoned);
    $this->sessionService->delete('questProgress');
    $this->questProgressRepository->updateQuestProgress($questProgress);
  }


  public function getMaxScore(int $questId): int
  {
    return $this->questService->getQuest($questId)->getMaxPoints();
  }
}