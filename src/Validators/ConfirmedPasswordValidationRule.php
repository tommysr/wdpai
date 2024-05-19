<?php

namespace App\Validator;

use App\Validator\IValid;

class ConfirmedPasswordValidationRule implements IValid
{
  private $password;

  public function __construct($password)
  {
    $this->password = $password;
  }

  public function validate($confirmedPassword): bool
  {
    return $this->password === $confirmedPassword;
  }
}