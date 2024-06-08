<?php

namespace App\Controllers;

use App\Controllers\AppController;
use App\Controllers\Interfaces\IRatingController;
use App\Middleware\IResponse;
use App\Middleware\RedirectResponse;
use App\Request\IFullRequest;
use App\Services\Authenticate\IAuthService;
use App\Services\QuestProgress\IQuestProgressManagementService;
use App\Services\QuestProgress\IQuestProgressRetrievalService;
use App\Services\Rating\IRatingService;
use App\Services\QuestProgress\IQuestProgressService;
use App\Services\Session\ISessionService;
use App\View\IViewRenderer;
use App\Models\Rating;

class RatingController extends AppController implements IRatingController
{
  private IRatingService $ratingService;
  private IQuestProgressRetrievalService $questProgressService;
  private IQuestProgressManagementService $questProgressManagement;
  private IAuthService $authService;

  public function __construct(IFullRequest $request, ISessionService $sessionService, IViewRenderer $viewRenderer, IRatingService $ratingService, IQuestProgressRetrievalService $questProgressService, IAuthService $authService, IQuestProgressManagementService $questProgressManagement)
  {
    parent::__construct($request, $sessionService, $viewRenderer);
    $this->ratingService = $ratingService;
    $this->questProgressService = $questProgressService;
    $this->authService = $authService;
    $this->questProgressManagement = $questProgressManagement;
  }

  public function postRating(IFullRequest $request): IResponse
  {
    $userId = $this->authService->getIdentity()->getId();
    $questProgress = $this->questProgressService->getCurrentProgress();

    if (!$questProgress->isCompleted()) {
      return new RedirectResponse('/error/404');
    }

    $rating = $this->request->getParsedBodyParam('rating');
    $rating = new Rating($userId, $questProgress->getQuestId(), (int) $rating);
    $this->ratingService->addRating($rating);
    $this->questProgressManagement->completeQuest();

    return new RedirectResponse('/play');
  }

  public function getRating(IFullRequest $request): IResponse
  {
    return $this->render('rating');
  }
}
