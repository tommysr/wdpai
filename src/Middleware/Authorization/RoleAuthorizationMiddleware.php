<?php

namespace App\Middleware\Authorization;

use App\Middleware\BaseMiddleware;
use App\Request\IFullRequest;
use App\Middleware\IResponse;
use App\Middleware\IHandler;
use App\Services\Authenticate\IAuthService;
use App\Middleware\BaseResponse;
use App\Services\Authorize\IAcl;


class AuthorizationMiddleware extends BaseMiddleware
{
  private IAcl $acl;
  private IAuthService $authService;

  public function __construct(IAcl $acl, IAuthService $authService)
  {
    $this->acl = $acl;
    $this->authService = $authService;
  }

  public function process(IFullRequest $request, IHandler $handler): IResponse
  {
    $identity = $this->authService->getIdentity();

    $role = $identity ? $identity->getRole() : 'guest';
    $resource = $request->getAttribute('resource');
    $privilege = $request->getAttribute('privilege');

    if (!$this->acl->isAllowed($role, $resource, $privilege)) {
      return new BaseResponse(403, [], 'Forbidden');
    }

    if ($this->next !== null) {
      return $this->next->handle($request);
    }
    
    return $handler->handle($request);
  }
}

