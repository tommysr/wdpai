<?php

namespace App\Validator;

use App\Validator\IValid;

class MinMaxIntValidationRule implements IValid
{
    private int $min;
    private int $max;

    public function __construct(int $min, int $max)
    {
        $this->min = $min;
        $this->max = $max;
    }

    public function validate($value): bool|string
    {
        if (!filter_var($value, FILTER_VALIDATE_INT)) {
            return 'Invalid number format';
        }

        return $value >= $this->min && $value <= $this->max;
    }
}