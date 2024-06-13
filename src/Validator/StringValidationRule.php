<?php

namespace App\Validator;

use App\Validator\IValid;

class StringValidationRule implements IValid
{
  public function validate($value): bool|string
  {
    if (!is_string($value)) {
      return 'Value must be a string.';
    }

    


    return is_string($value) ? true : 'Invalid string format';
  }
}