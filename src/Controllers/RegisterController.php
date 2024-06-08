<?php

namespace App\Controllers;

use App\Controllers\AppController;
use App\Controllers\Interfaces\IRegisterController;
use App\Middleware\JsonResponse;
use App\Middleware\IResponse;
use App\Services\Register\IRegisterService;
use App\Request\IFullRequest;
use App\Services\Session\ISessionService;
use App\View\IViewRenderer;

class RegisterController extends AppController implements IRegisterController
{
  private IRegisterService $registerService;

  public function __construct(
    IFullRequest $request,
    ISessionService $sessionService,
    IViewRenderer $viewRenderer,
    IRegisterService $registerService
  ) {
    parent::__construct($request, $sessionService, $viewRenderer);

    $this->registerService = $registerService;
  }

  public function getIndex(IFullRequest $request): IResponse
  {
    return $this->getRegister($request);
  }

  public function getRegister(IFullRequest $request): IResponse
  {
    return $this->render('register', ['title' => 'Sign up', 'message' => '']);
  }

  public function postRegister(IFullRequest $request): IResponse
  {
    $result = $this->registerService->register();

    if ($result->isValid()) {
      return new JsonResponse([]);
    }

    return new JsonResponse(['errors' => $result->getMessages()]);
  }
}