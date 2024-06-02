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
use App\Models\QuestState;
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


  public function postEnterQuest(IRequest $request, int $questId): IResponse
  {
    $walletId = $this->request->getParsedBodyParam('walletId');

    if (!$walletId) {
      return new RedirectResponse('/error/404', );
    }

    try {
      $this->questProgressService->startProgress($questId, (int) $walletId);

      return new RedirectResponse('/play');
    } catch (\Exception $e) {
      return new RedirectResponse('/error/404');
    }
  }

  public function getPlay(IRequest $request): IResponse
  {
    $questProgress = $this->questProgressService->getCurrentProgressFromSession();

    switch ($questProgress->getState()) {
      case QuestState::InProgress:
        $question = $this->questProgressService->getQuestion($questProgress->getQuestId(), $questProgress->getLastQuestionId());
        return $this->getNextQuestion($question);
      case QuestState::Unrated:
        return $this->getRating($request);
      case QuestState::Finished:
        return $this->getSummary($request);
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

  public function postAnswer(IRequest $request, int $questionId): IResponse
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

  public function getReset(IRequest $request): IResponse
  {
    $this->questProgressService->resetSession();
    return new RedirectResponse('/showQuests');
  }

  private function getSummary(IRequest $request): IResponse
  {

    $summary = $this->questProgressService->getQuestSummary($this->authService->getIdentity()->getId());

    return $this->render('questSummary', ['score' => $summary['score'], 'maxScore' => $summary['maxScore'], 'title' => 'Quest summary', 'better_than' => $summary['better_than']]);
  }

  public function postRating(IRequest $request): IResponse
  {
    $userId = $this->authService->getIdentity()->getId();
    $questProgress = $this->questProgressService->getCurrentProgressFromSession();

    if (!$questProgress->isCompleted()) {
      return new RedirectResponse('/error/404');
    }

    $rating = $this->request->getParsedBodyParam('rating');
    $rating = new Rating($userId, $questProgress->getQuestId(), (int) $rating);
    $this->ratingService->addRating($rating);

    $this->questProgressService->completeQuest();

    return new RedirectResponse('/play');
  }

  public function getRating(IRequest $request): IResponse
  {
    return $this->render('rating');
  }
}


