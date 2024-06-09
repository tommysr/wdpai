<?php

namespace App\Controllers;

use App\Controllers\Interfaces\IQuestionController;
use App\Middleware\IResponse;
use App\Middleware\RedirectResponse;
use App\Models\IQuestion;
use App\Models\QuestionType;
use App\Models\QuestState;
use App\Request\IFullRequest;
use App\Services\Authenticate\IAuthService;
use App\Services\Quest\IQuestProvider;
use App\Services\Question\IQuestionService;
use App\Services\QuestProgress\IQuestProgressManagementService;
use App\Services\QuestProgress\IQuestProgressManager;
use App\Services\QuestProgress\IQuestProgressProvider;
use App\Services\QuestProgress\IQuestProgressRetrievalService;
use App\Services\Quests\IQuestService;
use App\Services\Session\ISessionService;
use App\View\IViewRenderer;


class QuestionController extends AppController implements IQuestionController
{
  private IQuestProvider $questProvider;
  private IAuthService $authService;
  private IQuestProgressManager $questProgressManager;
  private IQuestProgressProvider $questProgressProvider;
  private IQuestionService $questionService;

  public function __construct(
    IFullRequest $request,
    ISessionService $sessionService,
    IViewRenderer $viewRenderer,
    IQuestProvider $questProvider,
    IAuthService $authService,
    IQuestProgressProvider $questProgressProvider,
    IQuestionService $questionService,
    IQuestProgressManager $questProgressManager
  ) {
    parent::__construct($request, $sessionService, $viewRenderer);
    $this->questProvider = $questProvider;
    $this->authService = $authService;
    $this->questProgressProvider = $questProgressProvider;
    $this->questionService = $questionService;
    $this->questProgressManager = $questProgressManager;
  }

  public function getIndex(IFullRequest $request): IResponse
  {
    return $this->getPlay($request);
  }

  public function getPlay(IFullRequest $request): IResponse
  {
    $questProgress = $this->questProgressProvider->getCurrentProgress();

    if (!$questProgress) {
      return new RedirectResponse('/error/403', ['you are not supposed to be here']);
    }

    switch ($questProgress->getState()) {
      case QuestState::InProgress:
        $question = $this->questionService->getQuestionWithOptions($questProgress->getLastQuestionId());
        return $this->getNextQuestion($question);
      case QuestState::Unrated:
        return new RedirectResponse('/rating/' . $questProgress->getQuestId());
      case QuestState::Finished:
        return new RedirectResponse('/summary/' . $questProgress->getQuestId());
      case QuestState::Abandoned:
        return new RedirectResponse('/showQuests');
      default:
        return new RedirectResponse('/error/500', ['internal server error']);
    }
  }

  private function getNextQuestion(IQuestion $question): IResponse
  {
    switch ($question->getType()) {
      case QuestionType::SINGLE_CHOICE->value:
        return $this->renderSingleChoiceQuestion($question);
      case QuestionType::MULTIPLE_CHOICE->value:
        return $this->renderMultipleChoiceQuestion($question);
      case QuestionType::READ_TEXT->value:
        return $this->renderReadTextQuestion($question);
      default:
        return new RedirectResponse('/error/500', ['internal server error']);
    }
  }

  private function renderSingleChoiceQuestion(IQuestion $question): IResponse
  {
    return $this->render('singleChoiceQuestion', ['question' => $question, 'title' => 'Choose answer']);
  }

  private function renderMultipleChoiceQuestion(IQuestion $question): IResponse
  {
    return $this->render('multipleChoiceQuestion', ['question' => $question, 'title' => 'Choose answers']);
  }

  private function renderReadTextQuestion(IQuestion $question): IResponse
  {
    return $this->render('readText', ['question' => $question, 'title' => 'Read text']);
  }

  public function postAnswer(IFullRequest $request, int $questionId): IResponse
  {
    $questProgress = $this->questProgressProvider->getCurrentProgress();
    $userId = $this->authService->getIdentity()->getId();
    $maxScore = $this->questProvider->getQuest($questProgress->getQuestId())->getMaxPoints();

    if ($questProgress->getLastQuestionId() !== $questionId) {
      return new RedirectResponse('/error/404');
    }

    $selectedOptions = $this->request->getParsedBodyParam('options') ?? [];
    $selectedOptionsInt = array_map('intval', $selectedOptions);
    $result = $this->questionService->evaluateOptions($questionId, $selectedOptionsInt);

    $this->questProgressManager->addPoints($result['points']);
    $this->questProgressManager->recordResponses($userId, $result['options']);
    $this->questProgressManager->changeProgress($questionId);

    return $this->renderQuestionSummary($result['points'], $result['maxPoints'], $questProgress->getScore() + $result['points'], $maxScore);
  }

  private function renderQuestionSummary(int $questionScore, int $questionMaxScore, int $score, int $maxScore): IResponse
  {
    $stars = 0;
    if ($questionMaxScore > 0) {
      $percentage = ($questionScore / $questionMaxScore) * 100;
      $stars = min(3, max(0, floor($percentage / 25)));
    }

    return $this->render('questionSummary', [
      'stars' => $stars,
      'questionScore' => $questionScore,
      'score' => $score,
      'maxScore' => $maxScore,
      'title' => 'Points gained'
    ]);
  }
}



