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
    $questId = $params[0] ? (int) $params[0] : null;

    if ($questRequest !== null) {
      $authResult = $this->questAuthorizeService->authorizeQuest($questRequest, $questId);

      if (!$authResult->isValid()) {
        return new RedirectResponse('/error/401');
      }
    }

    if ($this->next !== null) {
      return $this->next->handle($request);
    }

    return $handler->handle($request);
  }
}
