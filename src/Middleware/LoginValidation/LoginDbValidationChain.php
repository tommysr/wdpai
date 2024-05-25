<?php

namespace App\Middleware\LoginValidation;

use App\Validator\EmailValidationRule;
use App\Validator\MinLengthValidationRule;
use App\Validator\RequiredValidationRule;
use App\Validator\ValidationChain;

class LoginDbValidationChain extends ValidationChain
{
  public function __construct()
  {
    $this->addRules('email', [new RequiredValidationRule(), new EmailValidationRule()]);
    $this->addRules('password', [new RequiredValidationRule(), new MinLengthValidationRule(8)]);
  }
}
