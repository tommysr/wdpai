<?php

namespace App\Validator;

use App\Validator\IValid;

class MinMaxValidationRule implements IValid
{
    private int $min;
    private int $max;

    public function __construct(int $min, int $max)
    {
        $this->min = $min;
        $this->max = $max;
    }

    public function validate($value): bool|string {
        return $value >= $this->min && $value <= $this->max;
    }
}