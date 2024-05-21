<?php

namespace App\Services\Authorize;

use App\Services\Authorize\IAuthResult;

/**
 * Represents the result of an authorization attempt.
 */
class AuthResult implements IAuthResult
{
  private bool $valid;
  private string $identity;

  public function __construct(bool $valid, string $identity)
  {
    $this->valid = $valid;
    $this->identity = $identity;
  }

  public function isValid(): bool
  {
    return $this->valid;
  }

  public function getIdentity(): string
  {
    return $this->identity;
  }
}