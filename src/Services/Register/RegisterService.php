<?php

namespace App\Services\Register;

use App\Request\IFullRequest;
use App\Services\Register\IRegisterService;
use App\Services\Register\IRegisterStrategyFactory;

class RegisterService implements IRegisterService
{
  private IFullRequest $request;
  private IRegisterStrategyFactory $registerStrategyFactory;

  public function __construct(IFullRequest $request, IRegisterStrategyFactory $strategy = null)
  {
    $this->request = $request;
    $this->registerStrategyFactory = $strategy ?: new StrategyFactory($request);
  }

  public function register(): IRegisterResult
  {
    $method = $this->request->getParsedBodyParam('registration_method');
    $strategy = $this->registerStrategyFactory->create($method);
    return $strategy->register();
  }
}