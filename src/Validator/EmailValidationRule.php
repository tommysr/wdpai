<?php

namespace App\Validator;

use App\Validator\IValid;

class EmailValidationRule implements IValid
{
  public function validate($value): bool | string
  {
    return filter_var($value, FILTER_VALIDATE_EMAIL) ? true : 'Invalid email format';
  }
}