<?php

namespace App\Result;

use App\Result\IResult;

class Result implements IResult
{
  private array $messages;
  private bool $isValid;

  public function __construct(array $messages, bool $isValid = false)
  {
    $this->messages = $messages;
    $this->isValid = $isValid;
  }

  public function getMessages(): array
  {
    return $this->messages;
  }

  public function isValid(): bool
  {
    return $this->isValid;
  }
}