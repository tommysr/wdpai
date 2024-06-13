<?php
namespace App\Services\Register;

interface IRegisterStrategyFactory
{
  public function create(string $request): IRegisterStrategy;
  public function registerStrategy(string $method, IRegisterStrategy $strategy);
  public function getStrategies(): array;
}