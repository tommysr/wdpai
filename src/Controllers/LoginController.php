<?php

namespace App\Controllers;

use App\Request\IFullRequest;
use App\Request\IRequest;
use App\Middleware\IResponse;
use App\Middleware\RedirectResponse;
use App\Controllers\Interfaces\ILoginController;
use App\Services\Authenticate\IAuthService;


class LoginController extends AppController implements ILoginController
{
  private IAuthService $authService;

  public function __construct(IFullRequest $request, IAuthService $authService)
  {
    parent::__construct($request);
    $this->authService = $authService;
  }

  public function index(IRequest $request): IResponse
  {
    return $this->render('login', ['title' => 'Sign in', 'message' => '']);
  }

  public function login(IRequest $request): IResponse
  {
    return $this->render('login', ['title' => 'Sign in', 'message' => '']);
  }

  public function logout(IRequest $request): IResponse
  { 
    $this->authService->clearIdentity();
    return new RedirectResponse('/login');
  }
}
