<?php

namespace App\Services\Register;

use App\Services\Register\IRegisterResult;

class DBRegisterResult implements IRegisterResult
{
  private $messages;
  private $isValid;

  public function __construct(array $messages, bool $isValid = false)
  {
    $this->messages = $messages;
    $this->isValid = $isValid;
  }

  public function getMessages()
  {
    return $this->messages;
  }

  public function isValid()
  {
    return $this->isValid;
  }
}