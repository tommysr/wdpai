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
use App\Services\QuestProgress\IQuestProgressService;
use App\Services\Quests\IQuestService;
use App\Services\Recommendation\IRecommendationService;
use App\Services\Session\ISessionService;
use App\View\IViewRenderer;


class QuestionController extends AppController implements IQuestionController
{
  private IQuestService $questService;
  private IAuthService $authService;
  private IQuestProgressService $questProgressService;

  public function __construct(
    IFullRequest $request,
    ISessionService $sessionService,
    IViewRenderer $viewRenderer,
    IQuestService $questService,
    IAuthService $authService,
    IQuestProgressService $questProgressService,
    IRecommendationService $recommendationService
  ) {
    parent::__construct($request, $sessionService, $viewRenderer);
    $this->questService = $questService;
    $this->authService = $authService;
    $this->questProgressService = $questProgressService;
    $this->recommendationService = $recommendationService;
  }

  public function getIndex(IFullRequest $request): IResponse
  {
    return $this->getPlay($request);
  }

  public function getPlay(IFullRequest $request): IResponse
  {
    $questProgress = $this->questProgressService->getCurrentProgressFromSession();

    switch ($questProgress->getState()) {
      case QuestState::InProgress:
        $question = $this->questProgressService->getQuestion($questProgress->getQuestId(), $questProgress->getLastQuestionId());
        return $this->getNextQuestion($question);
      case QuestState::Unrated:
        return new RedirectResponse('/rating');
      case QuestState::Finished:
        return new RedirectResponse('/summary');
      case QuestState::Abandoned:
        return new RedirectResponse('/error/404');
      default:
        return new RedirectResponse('/error/404');
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
        throw new \Exception('unknown question type');
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
    $questProgress = $this->questProgressService->getCurrentProgressFromSession();

    if ($questProgress->getLastQuestionId() !== $questionId) {
      return new RedirectResponse('/error/404');
    }

    $selectedOptions = $this->request->getParsedBodyParam('options') ?? [];
    $selectedOptionsInt = array_map('intval', $selectedOptions);
    $result = $this->questProgressService->evaluateOptions($questionId, $selectedOptionsInt);
    $this->questProgressService->updateQuestProgress($result['points']);

    $userId = $this->authService->getIdentity()->getId();
    $this->questProgressService->recordResponses($userId, $result['options']);
    $this->questProgressService->adjustQuestProgress($questionId);
    $questProgress = $this->questProgressService->getCurrentProgressFromSession();
    $maxScore = $this->questProgressService->getMaxScore($questProgress->getQuestId());

    return $this->renderQuestionSummary($result['points'], $result['maxPoints'], $questProgress->getScore(), $maxScore);
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



