<?php

namespace App\Controllers;

use App\Controllers\AppController;
use App\Controllers\Interfaces\IQuestManagementController;
use App\Middleware\IResponse;
use App\Middleware\JsonResponse;
use App\Middleware\RedirectResponse;
use App\Models\IQuest;
use App\Request\IFullRequest;
use App\Services\Authenticate\IAuthService;
use App\Services\Quests\Builder\IQuestBuilderService;
use App\Services\Quests\IQuestService;
use App\Services\Session\ISessionService;
use App\View\IViewRenderer;

class QuestsManagementController extends AppController implements IQuestManagementController
{
  private IQuestService $questService;
  private IAuthService $authService;
  private IQuestBuilderService $questBuilderService;

  public function __construct(
    IFullRequest $request,
    ISessionService $sessionService,
    IViewRenderer $viewRenderer,
    IQuestService $questService,
    IAuthService $authService,
    IQuestBuilderService $questBuilderService
  ) {
    parent::__construct($request, $sessionService, $viewRenderer);
    $this->questService = $questService;
    $this->authService = $authService;
    $this->questBuilderService = $questBuilderService;
  }

  public function getIndex(IFullRequest $request): IResponse
  {
    return new RedirectResponse('/error/404', ['not found']);
  }

  private function renderEditAndCreateView(IQuest $quest = null): IResponse
  {
    return $this->render('layout', ['title' => 'quest add', 'quest' => $quest], 'createQuest');
  }

  public function getShowCreateQuest(IFullRequest $request): IResponse
  {
    return $this->renderEditAndCreateView();
  }

  public function getShowEditQuest(IFullRequest $request, int $questId): IResponse
  {
    $quest = $this->questService->getQuestWithQuestions($questId);

    if (!$quest) {
      return new RedirectResponse('/error/404', ['no such quest exist'], 0);
    }

    return $this->renderEditAndCreateView($quest);
  }

  public function postCreateQuest(IFullRequest $request): IResponse
  {
    $formData = $this->request->getBody();
    $parsedData = json_decode($formData, true);
    $creatorId = $this->authService->getIdentity()->getId();
    $parsedData['creatorId'] = $creatorId;
    $quest = $this->questBuilderService->buildQuest($parsedData);
    $questResult = $this->questService->createQuest($quest);

    if (!$questResult->isSuccess()) {
      return new JsonResponse(['errors' => $questResult->getMessages()]);
    } else {
      return new JsonResponse(['redirectUrl' => '/showCreatedQuests']);
    }
  }

  public function postEditQuest(IFullRequest $request, int $questId): IResponse
  {
    $formData = $this->request->getBody();
    $parsedData = json_decode($formData, true);
    $parsedData['questId'] = $questId;
    $quest = $this->questBuilderService->buildQuest($parsedData);
    $questResult = $this->questService->editQuest($quest);

    if (!$questResult->isSuccess()) {
      return new JsonResponse(['errors' => $questResult->getMessages()]);
    } else {
      return new JsonResponse(['redirectUrl' => '/showCreatedQuests']);
    }
  }

}