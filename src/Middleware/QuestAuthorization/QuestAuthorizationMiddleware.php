<?php

namespace App\Middleware\QuestAuthorization;

use App\Middleware\BaseMiddleware;
use App\Middleware\BaseResponse;
use App\Request\IFullRequest;
use App\Middleware\IResponse;
use App\Middleware\IHandler;
use App\Services\Authorize\QuestRequest;
use App\Services\Authorize\IQuestAuthorizeService;
use App\Services\Authorize\QuestAuthorizeService;

class QuestAuthorizationMiddleware extends BaseMiddleware
{
  private QuestRequest $questRequest;
  private IQuestAuthorizeService $questAuthorizeService;

  public function __construct(QuestRequest $questRequest, IQuestAuthorizeService $questAuthorizeService = null)
  {
    $this->questRequest = $questRequest;
    $this->questAuthorizeService = $questAuthorizeService ?: new QuestAuthorizeService();
  }

  public function process(IFullRequest $request, IHandler $handler): IResponse
  {
    $params = $request->getAttribute('params');
    $questId = $params ? $params['questId'] : null;

    $result = $this->questAuthorizeService->authorizeQuest($this->questRequest, $questId);

    if (!$result->isValid()) {
      return new BaseResponse('Unauthorized', [], 401);
    }

    if ($this->next !== null) {
      return $this->next->handle($request);
    }

    return $handler->handle($request);
  }
}
