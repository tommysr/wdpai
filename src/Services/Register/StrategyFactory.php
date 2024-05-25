<?php

namespace App\Services\Register;

use App\Repository\IUserRepository;
use App\Services\Register\IRegisterStrategyFactory;
use App\Request\IFullRequest;

class StrategyFactory implements IRegisterStrategyFactory
{
  private $strategies = [];

  private IUserRepository $userRepository;
  private IFullRequest $request;

  public function __construct(IFullRequest $request, IUserRepository $userRepository)
  {
    $this->request = $request;
    $this->userRepository = $userRepository;
  }

  public function registerStrategy(string $method, IRegisterStrategy $strategy)
  {
    $this->strategies[$method] = $strategy;
  }

  public function create(string $method): IRegisterStrategy
  {
    if (!isset($this->strategies[$method])) {
      throw new \Exception('Invalid method');
    }
    
    return $this->strategies[$method];
  }
}