<?php

namespace App\Middleware\LoginValidation;

use App\Validator\Factory\IValidationChainFactory;
use App\Validator\IValidationChain;

class LoginChainFactory implements IValidationChainFactory
{
  public function createValidationChain(string $type): IValidationChain
  {
    switch ($type) {
      case 'db':
        return new LoginDbValidationChain();
      default:
        throw new \InvalidArgumentException('Invalid login method');
    }
  }
}