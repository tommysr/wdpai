<?php

namespace App\Controllers;

use App\Controllers\AppController;
use App\Controllers\Interfaces\IQuestController;
use App\Middleware\IResponse;
use App\Middleware\JsonResponse;
use App\Middleware\RedirectResponse;
use App\Request\IFullRequest;
use App\Services\Authenticate\IAuthService;
use App\Services\QuestProgress\IQuestProgressManagementService;
use App\Services\QuestProgress\IQuestProgressRetrievalService;
use App\Services\QuestProgress\IQuestProgressService;
use App\Services\Quests\IQuestService;
use App\Services\Rating\IRatingService;
use App\Services\Session\ISessionService;
use App\View\IViewRenderer;


class QuestController extends AppController implements IQuestController
{
  private IQuestProgressManagementService $questProgressManagement;
  private IQuestProgressRetrievalService $questProgressRetrieval;
  private IAuthService $authService;
  private IRatingService $ratingService;
  private IQuestService $questService;

  public function __construct(IFullRequest $request, ISessionService $sessionService, IViewRenderer $viewRenderer, IQuestProgressRetrievalService $questProgressRetrieval, IAuthService $authService, IRatingService $ratingService, IQuestProgressManagementService $questProgressManagement)
  {
    parent::__construct($request, $sessionService, $viewRenderer);
    $this->questProgressRetrieval = $questProgressRetrieval;
    $this->authService = $authService;
    $this->ratingService = $ratingService;
    $this->questProgressManagement = $questProgressManagement;
  }

  public function postAbandonQuest(IFullRequest $request): IResponse
  {
    $this->questProgressManagement->abandonQuest();
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
      $this->questProgressManagement->startProgress($questId, (int) $walletId);

      return new JsonResponse(['redirect' => '/play']);
    } catch (\Exception $e) {
      return new JsonResponse(['errors' => ['Could not start quest']]);
    }
  }

  public function getReset(IFullRequest $request): IResponse
  {
    $this->questProgressManagement->resetSession();
    return new RedirectResponse('/showQuests');
  }

  public function getSummary(IFullRequest $request, int $questId): IResponse
  {
    $userId = $this->authService->getIdentity()->getId();
    $summary = $this->questProgressRetrieval->getQuestSummary($this->authService->getIdentity()->getId(), $questId);

    return $this->render('questSummary', ['score' => $summary['score'], 'maxScore' => $summary['maxScore'], 'title' => 'Quest summary', 'better_than' => $summary['better_than']]);
  }
}


