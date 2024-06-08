<?php

namespace App\Controllers;

use App\Request\IFullRequest;
use App\Middleware\IResponse;
use App\Middleware\RedirectResponse;
use App\Controllers\Interfaces\ILoginController;
use App\Services\Authenticate\IAuthService;
use App\Services\Session\ISessionService;
use App\View\IViewRenderer;


class LoginController extends AppController implements ILoginController
{
  private IAuthService $authService;


  public function __construct(IFullRequest $request, IAuthService $authService, ISessionService $sessionService, IViewRenderer $viewRenderer)
  {
    parent::__construct($request, $sessionService, $viewRenderer);
    $this->authService = $authService;
  }

  public function getIndex(IFullRequest $request): IResponse
  {
    return $this->getLogin($request);
  }

  public function getLogin(IFullRequest $request): IResponse
  {
    return $this->render('login', ['title' => 'Sign in', 'message' => '']);
  }

  public function getLogout(IFullRequest $request): IResponse
  {
    $this->authService->clearIdentity();

    return new RedirectResponse('/login');
  }
}
