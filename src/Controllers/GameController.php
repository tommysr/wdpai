<?php

namespace App\Controllers;

use App\Controllers\AppController;
use App\Controllers\Interfaces\IGameController;
use App\Middleware\IResponse;
use App\Middleware\JsonResponse;
use App\Middleware\RedirectResponse;
use App\Models\Interfaces\IQuestProgress;
use App\Models\IQuestion;
use App\Models\QuestionType;
use App\Models\QuestProgress;
use App\Models\Rating;
use App\Repository\IOptionsRepository;
use App\Repository\IQuestionsRepository;
use App\Repository\OptionsRepository;
use App\Repository\QuestionsRepository;
use App\Request\IFullRequest;
use App\Request\IRequest;
use App\Services\Authenticate\AuthenticateService;
use App\Services\Authenticate\IAuthService;
use App\Services\QuestProgress\IQuestProgressService;
use App\Services\QuestProgress\QuestProgressService;
use App\Services\Rating\IRatingService;
use App\Services\Rating\RatingService;
use App\Services\Session\ISessionService;


class GameController extends AppController implements IGameController
{
  private IQuestProgressService $questProgressService;
  private IAuthService $authService;
  private IRatingService $ratingService;


  public function __construct(IFullRequest $request, ISessionService $sessionService = null, IQuestProgressService $questProgressService = null, IAuthService $authService = null, IRatingService $ratingService = null)
  {
    parent::__construct($request, $sessionService);
    $this->questProgressService = $questProgressService ?: new QuestProgressService($this->sessionService);
    $this->authService = $authService ?: new AuthenticateService($this->sessionService);
    $this->ratingService = $ratingService ?: new RatingService();
  }

  public function getIndex(IRequest $request): IResponse
  {
    return new JsonResponse([]);
  }

  public function getPlay(IRequest $request): IResponse
  {
    $questProgress = $this->questProgressService->getCurrentProgressFromSession();
    return $this->showNextQuestion($questProgress);
  }

  private function showNextQuestion(IQuestProgress $questProgress): IResponse
  {
    $question = $this->questProgressService->getNextQuestion($questProgress->getQuestId(), $$questProgress->getLastQuestionId());

    switch ($question->getType()) {
      case QuestionType::SINGLE_CHOICE:
        return $this->renderSingleChoiceQuestion($question);
      case QuestionType::MULTIPLE_CHOICE:
        return $this->renderMultipleChoiceQuestion($question);
      default:
        return $this->renderReadTextQuestion($question);
    }
  }

  private function renderSingleChoiceQuestion(IQuestion $question): IResponse
  {
    return $this->render('singleChoiceQuestion', ['question' => $question]);
  }

  private function renderMultipleChoiceQuestion(IQuestion $question): IResponse
  {
    return $this->render('multipleChoiceQuestion', ['question' => $question]);
  }

  private function renderReadTextQuestion(IQuestion $question): IResponse
  {
    return $this->render('readText', ['question' => $question]);
  }


  public function postAnswer(IRequest $request, int $questionId): IResponse
  {
    $userId = $this->authService->getIdentity()->getId();
    $selectedOptions = $this->request->getParsedBodyParam('options') ?? [];

    $result = $this->questProgressService->evaluateOptions($questionId, $selectedOptions);
    $this->questProgressService->updateQuestProgress($result['points']);
    $this->questProgressService->recordResponses($userId, $result['options']);

    $questProgress = $this->questProgressService->getCurrentProgressFromSession();
    $maxScore = $this->questProgressService->getMaxScore($questProgress->getQuestId());

    if (!$questProgress->isCompleted()) {
      return $this->renderQuestionSummary($result['points'], $result['maxPoints'], $questProgress->getScore(), $maxScore);
    } else {
      return $this->getRating($request);
    }
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
      'questionMaxScore' => $questionMaxScore,
      'score' => $score,
      'questMaxScore' => $maxScore,
    ]);
  }

  private function getSummary(IRequest $request): IResponse
  {
    $questProgress = $this->questProgressService->getCurrentProgressFromSession();

    if (!$questProgress->isCompleted()) {
      return new RedirectResponse('/error/404');
    }

    // $questSummary = $this->questProgressService->getQuestSummary($questProgress->getQuestId(), $this->authService->getUserId());
    return $this->render('questSummary', ['points' => $questProgress->getScore()]);
  }

  public function postRating(IRequest $request): IResponse
  {
    $userId = $this->authService->getIdentity()->getId();
    $questProgress = $this->questProgressService->getCurrentProgressFromSession();

    if (!$questProgress->isCompleted()) {
      return new RedirectResponse('/error/404');
    }

    $rating = $this->request->getParsedBodyParam('rating');
    $rating = new Rating($userId, $questProgress->getQuestId(), $rating);
    $this->ratingService->addRating($rating);

    return new RedirectResponse('/summary');
  }

  public function getRating(IRequest $request): IResponse
  {
    return $this->render('rating');
  }
}


