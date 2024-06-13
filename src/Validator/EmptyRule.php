<?php
namespace App\Validator;

use App\Validator\IValid;

class EmptyRule implements IValid
{
  public function validate($value): bool|string
  {
    return true;
  }
}