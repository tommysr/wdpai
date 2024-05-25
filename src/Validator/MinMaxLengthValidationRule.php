<?php

namespace App\Validator;

use App\Validator\IValid;

class MinMaxLengthValidationRule implements IValid
{
  private int $minLength;
  private int $maxLength;
  public function __construct(int $minLength, int $maxLength)
  {
    $this->minLength = $minLength;
    $this->maxLength = $maxLength;
  }

  public function validate($value): bool|string
  {
    $length = strlen($value);
    if ($length < $this->minLength) {
      return "Value is too short";
    }

    if ($length > $this->maxLength) {
      return "Value is too long";
    }

    return true;
  }
}