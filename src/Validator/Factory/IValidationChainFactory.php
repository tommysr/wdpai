<?php

namespace App\Validator\Factory;

use App\Validator\IValidationChain;

interface IValidationChainFactory
{
  public function createValidationChain(string $type): IValidationChain;
}