<?php

namespace App\Controllers;

use App\Request\IFullRequest;
use App\Request\IRequest;
use App\Middleware\IResponse;
use App\Middleware\RedirectResponse;
use App\Controllers\Interfaces\ILoginController;
use App\Services\Authenticate\IAuthService;
use App\Services\Authenticate\AuthenticateService;


class LoginController extends AppController implements ILoginController
{
  private IAuthService $authService;

  public function __construct(IFullRequest $request, IAuthService $authService = null)
  {
    parent::__construct($request);
    $this->authService = $authService ?: new AuthenticateService($this->sessionService);
  }

  public function getIndex(IRequest $request): IResponse
  {
    return $this->getLogin($request);
  }

  public function getLogin(IRequest $request): IResponse
  {
    return $this->render('login', ['title' => 'Sign in', 'message' => '']);
  }

  public function getLogout(IRequest $request): IResponse
  {
    $this->authService->clearIdentity();
    return new RedirectResponse('/login');
  }
}
