<?php

namespace App\Middleware\LoginValidation;

use App\Middleware\InputValidation\InputValidationMiddleware;
use App\Validator\EmailValidationRule;
use App\Validator\IValidationChain;
use App\Validator\MinLengthValidationRule;
use App\Validator\RequiredValidationRule;
use App\Validator\ValidationChain;

class LoginValidationMiddleware extends InputValidationMiddleware
{
  public function __construct(IValidationChain $validationChain = null)
  {
    $validationChain = $validationChain ?: new ValidationChain();
    $validationChain->addRules('email', [new RequiredValidationRule(), new EmailValidationRule()]);
    $validationChain->addRules('password', [new RequiredValidationRule(), new MinLengthValidationRule(8)]);
    parent::__construct($validationChain);
  }
}