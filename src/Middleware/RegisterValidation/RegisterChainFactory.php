<?php

namespace App\Middleware\RegisterValidation;

use App\Validator\Factory\IValidationChainFactory;
use App\Validator\IValidationChain;
use App\Middleware\RegisterValidation\RegisterDbValidationChain;

class RegisterChainFactory implements IValidationChainFactory
{
  public function createValidationChain(string $type): IValidationChain
  {
    switch ($type) {
      case 'db':
        return new RegisterDbValidationChain();
      default:
        throw new \InvalidArgumentException('Invalid registration method');
    }
  }
}