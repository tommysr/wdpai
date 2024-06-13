<?php

namespace App\Services\Register;

use App\Services\Register\IRegisterStrategyFactory;
use App\Request\IFullRequest;

class StrategyFactory implements IRegisterStrategyFactory
{
  private array $strategies = [];

  public function registerStrategy(string $method, IRegisterStrategy $strategy)
  {
    $this->strategies[$method] = $strategy;
  }

  public function create(string $method): IRegisterStrategy
  {
    if (!isset($this->strategies[$method])) {
      throw new InvalidRegisterMethodException();
    }

    return $this->strategies[$method];
  }

  public function getStrategies(): array
  {
    return $this->strategies;
  }
}

class InvalidRegisterMethodException extends \Exception
{
  public function __construct()
  {
    parent::__construct('Invalid method');
  }
}