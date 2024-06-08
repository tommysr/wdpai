<?php

namespace App\Controllers;

use App\Controllers\AppController;
use App\Controllers\Interfaces\IQuestController;
use App\Middleware\IResponse;
use App\Middleware\JsonResponse;
use App\Middleware\RedirectResponse;
use App\Request\IFullRequest;
use App\Services\Authenticate\IAuthService;
use App\Services\QuestProgress\IQuestProgressService;
use App\Services\Rating\IRatingService;
use App\Services\Session\ISessionService;
use App\View\IViewRenderer;


class GameController extends AppController implements IQuestController
{
  private IQuestProgressService $questProgressService;
  private IAuthService $authService;
  private IRatingService $ratingService;

  public function __construct(IFullRequest $request, ISessionService $sessionService, IViewRenderer $viewRenderer, IQuestProgressService $questProgressService, IAuthService $authService, IRatingService $ratingService)
  {
    parent::__construct($request, $sessionService, $viewRenderer);
    $this->questProgressService = $questProgressService;
    $this->authService = $authService;
    $this->ratingService = $ratingService;
  }

  public function postAbandonQuest(IFullRequest $request): IResponse
  {
    $this->questProgressService->abandonQuest();
    return new JsonResponse(['message' => 'Quest abandoned']);
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
      $this->questProgressService->startProgress($questId, (int) $walletId);

      return new JsonResponse(['redirect' => '/play']);
    } catch (\Exception $e) {
      return new JsonResponse(['errors' => ['Could not start quest']]);
    }
  }

  public function getReset(IFullRequest $request): IResponse
  {
    $this->questProgressService->resetSession();
    return new RedirectResponse('/showQuests');
  }

  public function getSummary(IFullRequest $request): IResponse
  {
    $summary = $this->questProgressService->getQuestSummary($this->authService->getIdentity()->getId());

    return $this->render('questSummary', ['score' => $summary['score'], 'maxScore' => $summary['maxScore'], 'title' => 'Quest summary', 'better_than' => $summary['better_than']]);
  }
}


