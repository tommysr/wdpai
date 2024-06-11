<?php

namespace App\Controllers;

use App\Controllers\AppController;
use App\Controllers\Interfaces\IAdminController;
use App\Middleware\JsonResponse;
use App\Middleware\RedirectResponse;
use App\Repository\IUserRepository;
use App\Request\IFullRequest;
use App\Middleware\IResponse;
use App\Services\Quests\IQuestManager;
use App\Services\Quests\IQuestService;
use App\Services\Recommendation\IRecommendationService;
use App\Services\Session\ISessionService;
use App\Services\User\IUserService;
use App\View\IViewRenderer;

class AdminController extends AppController implements IAdminController
{
  private IQuestManager $questManager;
  private IRecommendationService $recommendationService;
  private IUserService $userService;

  public function __construct(IFullRequest $request, ISessionService $sessionService, IViewRenderer $viewRenderer, IQuestManager $questManager, IRecommendationService $recommendationService, IUserService $userService)
  {
    parent::__construct($request, $sessionService, $viewRenderer);
    $this->questManager = $questManager;
    $this->recommendationService = $recommendationService;
    $this->userService = $userService;
  }


  public function getIndex(IFullRequest $request): IResponse
  {
    return new RedirectResponse('/error/404', ['unknown route']);
  }

  public function postPublishQuest(IFullRequest $request, int $questId): IResponse
  {
    $this->questManager->publishQuest($questId);
    return new JsonResponse(['message' => 'quest published']);
  }

  public function postUnpublishQuest(IFullRequest $request, int $questId): IResponse
  {
    $this->questManager->unpublishQuest($questId);
    return new JsonResponse(['message' => 'quest unpublished']);
  }

  public function getRefreshRecommendations(IFullRequest $request): IResponse
  {
    $this->recommendationService->refreshRecommendations();
    return new JsonResponse(['message' => 'recommendations refreshed']);
  }

  public function getPromoteUser(IFullRequest $request, string $userName): IResponse
  {
    $this->userService->promoteToCreator($userName);
    return new JsonResponse(['role set']);
  }
}