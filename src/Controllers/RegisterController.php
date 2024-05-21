<?php

namespace App\Controllers;

use App\Controllers\AppController;
use App\Controllers\IRegisterController;
use App\Middleware\JsonResponse;
use App\Middleware\RedirectResponse;
use App\Request\IRequest;
use App\Middleware\IResponse;
use App\Services\Register\IRegisterService;
use App\Services\Register\RegisterService;
use App\Request\IFullRequest;

class RegisterController extends AppController implements IRegisterController
{
  private IRegisterService $registerService;

  public function __construct(IFullRequest $request, IRegisterService $registerer = null)
  {
    parent::__construct($request);
    $this->registerer = $registerer ?: new RegisterService($this->request);
  }

  public function index(IRequest $request): IResponse
  {
    return $this->renderRegisterView();
  }

  public function register(IRequest $request): IResponse
  {
    $result = $this->registerService->register($this->request->getParsedBody());

    if ($result->isValid()) {
      return new RedirectResponse('/login');
    }

    return new JsonResponse($result->getMessages());
  }

  private function renderRegisterView(string $message = '')
  {
    return $this->render('register', ['title' => 'Sign up', 'message' => $message]);
  }
}