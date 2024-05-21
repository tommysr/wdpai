<?php

namespace App\Services\Authorize;

use App\Services\Authorize\IAuthResult;

/**
 * Represents the result of an authorization attempt.
 */
class AuthResult implements IAuthResult
{
  private bool $valid;
  private array $messages;


  public function __construct(bool $valid, array $messages = [])
  {
    $this->valid = $valid;
    $this->messages = $messages;
  }

  public function isValid(): bool
  {
    return $this->valid;
  }

  public function getMessages(): array
  {
    return $this->messages;
  }
}