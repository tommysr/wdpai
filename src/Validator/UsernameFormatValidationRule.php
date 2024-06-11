<?php
namespace App\Validator;

use App\Validator\IValid;

class UsernameFormatValidationRule implements IValid
{
  public function validate($value): bool|string
  {
    return preg_match('/^[a-zA-Z0-9_]+$/', $value) === 1 ? true : 'Invalid username format';
  }
}
