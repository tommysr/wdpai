<?php

namespace App\Controllers;

use App\Controllers\AppController;
use App\Controllers\Interfaces\IRatingController;
use App\Middleware\IResponse;
use App\Middleware\RedirectResponse;
use App\Request\IFullRequest;
use App\Services\Authenticate\IAuthService;
use App\Services\QuestProgress\IQuestProgressProvider;
use App\Services\Rating\IRatingService;
use App\Services\Session\ISessionService;
use App\View\IViewRenderer;
use App\Models\Rating;

class RatingController extends AppController implements IRatingController
{
  private IRatingService $ratingService;
  private IQuestProgressProvider $questProgressProvider;
  private IAuthService $authService;

  public function __construct(IFullRequest $request, ISessionService $sessionService, IViewRenderer $viewRenderer, IRatingService $ratingService, IQuestProgressProvider $questProgressProvider, IAuthService $authService)
  {
    parent::__construct($request, $sessionService, $viewRenderer);
    $this->ratingService = $ratingService;
    $this->questProgressProvider = $questProgressProvider;
    $this->authService = $authService;
  }

  public function postRating(IFullRequest $request, int $questId): IResponse
  {
    $userId = $this->authService->getIdentity()->getId();
    $questProgress = $this->questProgressProvider->getCurrentProgress();

    if (!$questProgress || !$questProgress->isCompleted()) {
      return new RedirectResponse('/error/404');
    }

    $rating = $this->request->getParsedBodyParam('rating');
    $rating = new Rating($userId, $questId, (int) $rating);
    $this->ratingService->addRating($rating);

    return new RedirectResponse('/play');
  }

  public function getRating(IFullRequest $request, int $questId): IResponse
  {
    return $this->render('rating', ['questId' => $questId]);
  }
}
