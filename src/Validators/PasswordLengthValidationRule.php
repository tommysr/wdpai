<?php
namespace App\Validator;

use App\Validator\IValid;

class PasswordLengthValidationRule implements IValid
{
  public function validate($value): bool
  {
    return strlen($value) >= 8;
  }
}