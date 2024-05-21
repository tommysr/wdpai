<?php

namespace App\Validator;

use App\Validator\IValid;

class RequiredValidationRule implements IValid
{
  public function validate($value): bool | string
  {
    return !empty($value) ? true : 'Field is required';
  }
}