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
use App\Services\Authenticate\IIdentity;
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

  public function getCurrentProgress(int $userId, int $questId): IQuestProgress
  {
    $sessionProgress = $this->sessionService->get('questProgress');
    $progress = $sessionProgress ?? $this->questProgressRepository->getQuestProgress($userId, $questId);

    if (!$progress) {
      throw new \Exception("Quest progress not found");
    }

    if ($progress->isCompleted()) {
      throw new \Exception("Quest is already completed");
    }

    return $progress;
  }

  public function getCurrentProgressFromSession(): IQuestProgress
  {
    $progress = $this->sessionService->get('questProgress');

    if (!$progress) {
      throw new \Exception("Quest progress not found");
    }

    if ($progress->isCompleted()) {
      throw new \Exception("Quest is already completed");
    }

    return $progress;
  }

  public function getNextQuestion(int $questId, int $questionId): IQuestion
  {
    $nextQuestion = $this->questionsRepository->getNextQuestion($questId, $questionId);

    if (!$nextQuestion) {
      throw new \Exception("Next question not found");
    }

    $options = $this->optionsRepository->getOptionsByQuestionId($nextQuestion->getQuestionId());
    $nextQuestion->setOptions($options);

    return $nextQuestion;
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

    $questProgress = new QuestProgress(null, 0, $questId, $walletId, 0, QuestState::InProgress);

    $this->questProgressRepository->saveQuestProgress($questProgress);
    $this->sessionService->set('questProgress', $questProgress);
  }

  public function evaluateOptions(int $questionId, array $selectedOptions): array
  {

    $question = $this->questionsRepository->getById($questionId);

    if ($question->getType() === QuestionType::READ_TEXT) {
      return ['points' => $question->getPoints(), 'options' => []];
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

  public function recordResponses(int $userId, array $selectedOptions): void
  {
    $questProgress = $this->getCurrentProgressFromSession();
    $questionId = $this->questionsRepository->getNextQuestionId($questProgress->getQuestId(), $questProgress->getLastQuestionId());

    if (!$questionId) {
      $this->completeQuest();
      return;
    }

    $this->questProgressRepository->saveResponses($userId, $selectedOptions);
    $questProgress->setNextQuestionId($questionId);

    $this->sessionService->set('questProgress', $questProgress);
    $this->questProgressRepository->updateQuestProgress($questProgress);
  }

  public function abandonQuest(): void
  {
    $questProgress = $this->getCurrentProgressFromSession();
    $questProgress->setState(QuestState::Abandoned);
    $this->sessionService->set('questProgress', $questProgress);
    $this->questProgressRepository->updateQuestProgress($questProgress);
  }

  public function getMaxScore(int $questId): int
  {
    return $this->questService->getQuest($questId)->getMaxPoints();
  }
}