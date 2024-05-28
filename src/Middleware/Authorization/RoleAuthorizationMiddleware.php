<?php

namespace App\Middleware\Authorization;

use App\Middleware\BaseMiddleware;
use App\Middleware\RedirectResponse;
use App\Models\UserRole;
use App\Request\IFullRequest;
use App\Middleware\IResponse;
use App\Middleware\IHandler;
use App\Services\Authenticate\IAuthService;
use App\Services\Authorize\IAcl;


class RoleAuthorizationMiddleware extends BaseMiddleware
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
    $role = $identity ? $identity->getRole()->getName() : (string) UserRole::GUEST;
    $resource = $request->getAttribute('controller');
    $privilege = $request->getAttribute('action');

    if (!$this->acl->isAllowed($role, $resource, $privilege)) {
      return new RedirectResponse('/error/401');
    }

    if ($this->next !== null) {
      return $this->next->process($request, $handler);
    }

    return $handler->handle($request);
  }
}

