<?php

namespace App\Controllers;

use App\Controllers\AppController;
use App\Controllers\Interfaces\IQuestController;
use App\Middleware\IResponse;
use App\Middleware\JsonResponse;
use App\Middleware\RedirectResponse;
use App\Request\IFullRequest;
use App\Services\Authenticate\IAuthService;
use App\Services\QuestProgress\IQuestProgressManager;
use App\Services\QuestProgress\IQuestProgressProvider;
use App\Services\Rating\IRatingService;
use App\Services\Session\ISessionService;
use App\View\IViewRenderer;


class QuestController extends AppController implements IQuestController
{
  private IQuestProgressManager $questProgressManager;
  private IQuestProgressProvider $questProgressProvider;
  private IAuthService $authService;
  private IRatingService $ratingService;

  public function __construct(IFullRequest $request, ISessionService $sessionService, IViewRenderer $viewRenderer, IQuestProgressProvider $questProgressProvider, IAuthService $authService, IRatingService $ratingService, IQuestProgressManager $questProgressManager)
  {
    parent::__construct($request, $sessionService, $viewRenderer);
    $this->questProgressProvider = $questProgressProvider;
    $this->authService = $authService;
    $this->ratingService = $ratingService;
    $this->questProgressManager = $questProgressManager;
  }

  public function postAbandonQuest(IFullRequest $request): IResponse
  {
    $this->questProgressManager->abandonQuest();
    return new JsonResponse(['message' => 'Quest abandoned']);
  }

  public function postCompleteQuest(IFullRequest $request): IResponse
  {
    $this->questProgressManager->completeQuest();

    return new RedirectResponse('/showQuests');
  }

  public function getIndex(IFullRequest $request): IResponse
  {
    return new JsonResponse([]);
  }


  public function postEnterQuest(IFullRequest $request, int $questId): IResponse
  {
    $walletId = $this->request->getParsedBodyParam('walletId');

    if (!$walletId) {
      return new JsonResponse(['errors' => ['Wallet id is required']]);
    }

    try {
      $this->questProgressManager->startProgress($questId, (int) $walletId);

      return new JsonResponse(['redirect' => '/play']);
    } catch (\Exception $e) {
      error_log($e->getMessage());
      return new JsonResponse(['errors' => ['Could not start quest']]);
    }
  }

  public function getReset(IFullRequest $request): IResponse
  {
    $this->questProgressManager->resetSession();
    return new RedirectResponse('/dashboard');
  }

  public function getSummary(IFullRequest $request, int $questId): IResponse
  {
    $userId = $this->authService->getIdentity()->getId();
    $summary = $this->questProgressProvider->getQuestSummary($this->authService->getIdentity()->getId(), $questId);

    return $this->render('questSummary', ['score' => $summary['score'], 'maxScore' => $summary['maxScore'], 'title' => 'Quest summary', 'better_than' => $summary['better_than']]);
  }
}


