<?php
namespace App\Validator;

use App\Validator\IValid;

class MinLengthValidationRule implements IValid
{
  private int $minLength;
  public function __construct(int $minLength)
  {
    $this->minLength = $minLength;
  }

  public function validate($value): bool
  {
    return strlen($value) >= $this->minLength ? true : 'Value is too short';
  }
}