<?php

namespace App\Middleware\RegisterValidation;

use App\Validator\EmailValidationRule;
use App\Validator\MinLengthValidationRule;
use App\Validator\RequiredValidationRule;
use App\Validator\UsernameFormatValidationRule;
use App\Validator\ValidationChain;

class RegisterDbValidationChain extends ValidationChain
{
  public function __construct()
  {
    $this->addRules('email', [new RequiredValidationRule(), new EmailValidationRule()]);
    $this->addRules('password', [new RequiredValidationRule(), new MinLengthValidationRule(8)]);
    $this->addRules('username', [new RequiredValidationRule(), new UsernameFormatValidationRule()]);
  }
}