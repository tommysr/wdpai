<?php

namespace App\Middleware\LoginValidation;

use App\Validator\EmailValidationRule;
use App\Validator\MinLengthValidationRule;
use App\Validator\MinMaxLengthValidationRule;
use App\Validator\RequiredValidationRule;
use App\Validator\ValidationChain;

class LoginDbValidationChain extends ValidationChain
{
  public function __construct()
  {
    $this->addRule('login_method', new RequiredValidationRule());
    $this->addRules('email', [new RequiredValidationRule(), new EmailValidationRule()]);
    $this->addRules('password', [new RequiredValidationRule(), new MinMaxLengthValidationRule(8, 255)]);
  }
}
