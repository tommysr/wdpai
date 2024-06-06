<?php

namespace App\Middleware\QuestAuthorization;

use App\Middleware\BaseMiddleware;
use App\Middleware\RedirectResponse;
use App\Request\IFullRequest;
use App\Middleware\IResponse;
use App\Middleware\IHandler;
use App\Services\Authorize\Quest\QuestRequest;
use App\Services\Authorize\Quest\IQuestAuthorizeService;

class QuestAuthorizationMiddleware extends BaseMiddleware
{
  private IQuestAuthorizeService $questAuthorizeService;

  public function __construct(IQuestAuthorizeService $questAuthorizeService)
  {
    $this->questAuthorizeService = $questAuthorizeService;
  }

  public function process(IFullRequest $request, IHandler $handler): IResponse
  {
    $params = $request->getAttribute('params');
    $requestAction = $request->getAttribute('action');
    $questRequest = QuestRequest::from($requestAction);
    $questId = isset($params['questId']) ? (int) $params['questId'] : null;

    if ($questRequest !== null) {
      $authResult = $this->questAuthorizeService->authorizeQuest($questRequest, $questId);

      $redirect = $authResult->getRedirectUrl();

      if ($redirect !== null && $redirect !== $request->getPath()) {
        return new RedirectResponse($redirect);
      }

      if (!$authResult->isValid()) {
        return new RedirectResponse('/error/401');
      }
    }

    if ($this->next !== null) {
      return $this->next->process($request, $handler);
    }

    return $handler->handle($request);
  }
}
