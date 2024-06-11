<?php

namespace App\Validator;

use App\Validator\IValid;

class MinMaxFloatValidationRule implements IValid
{
  private float $min;
  private float $max;

  public function __construct(float $min, float $max)
  {
    $this->min = $min;
    $this->max = $max;
  }

  public function validate($value): bool|string
  {
    if (!filter_var($value, FILTER_VALIDATE_FLOAT)) {
      return 'Invalid number format';
    }
    
    return $value >= $this->min && $value <= $this->max;
  }
}