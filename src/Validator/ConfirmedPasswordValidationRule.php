<?php

namespace App\Validator;

use App\Validator\IValid;

class ConfirmedPasswordValidationRule implements IValid
{
  private string $password;

  public function __construct(string $password)
  {
    $this->password = $password;
  }

  public function validate($confirmedPassword): bool|string
  {
    return $this->password === $confirmedPassword ? true : 'Passwords do not match';
  }
}