<?php

namespace App\Controllers;

use App\Controllers\AppController;
use App\Controllers\Interfaces\IRegisterController;
use App\Middleware\JsonResponse;
use App\Middleware\RedirectResponse;
use App\Repository\UserRepository;
use App\Request\IRequest;
use App\Middleware\IResponse;
use App\Services\Register\DbRegisterStrategy;
use App\Services\Register\IRegisterService;
use App\Services\Register\IRegisterStrategyFactory;
use App\Services\Register\RegisterService;
use App\Request\IFullRequest;
use App\Services\Register\StrategyFactory;

class RegisterController extends AppController implements IRegisterController
{
  private IRegisterService $registerService;

  public function __construct(IFullRequest $request, IRegisterService $registerService = null)
  {
    parent::__construct($request);
    $this->registerService = $registerService ?: $this->initializeRegisterService();
  }

  private function initializeRegisterService(): IRegisterService
  {
    $userRepository = new UserRepository();
    $strategyFactory = new StrategyFactory($this->request, $userRepository);
    $strategyFactory->registerStrategy('db', new DbRegisterStrategy($this->request, $userRepository));
    return new RegisterService($this->request, $userRepository, $strategyFactory);
  }

  public function getIndex(IRequest $request): IResponse
  {
    return $this->getRegister($request);
  }

  public function getRegister(IRequest $request): IResponse
  {
    return $this->render('register', ['title' => 'Sign up', 'message' => '']);
  }

  public function postRegister(IRequest $request): IResponse
  {
    $result = $this->registerService->register();

    if ($result->isValid()) {
      return new JsonResponse([]);
    }

    return new JsonResponse(['errors' => $result->getMessages()]);
  }
}