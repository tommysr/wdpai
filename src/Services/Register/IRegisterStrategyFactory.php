<?php
namespace App\Services\Register;

interface IRegisterStrategyFactory
{
  public function create(string $request): IRegisterStrategy;
}