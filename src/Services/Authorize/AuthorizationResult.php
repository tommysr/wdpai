<?php

namespace App\Services\Authorize;

use App\Services\Authorize\IAuthResult;

/**
 * Represents the result of an authorization attempt.
 */
class AuthorizationResult implements IAuthResult
{
  private bool $valid;
  private array $messages;
  private ?string $redirectUrl = null;


  public function __construct(array $messages = [], bool $valid = false, string $redirectUrl = null)
  {
    $this->valid = $valid;
    $this->messages = $messages;
    $this->redirectUrl = $redirectUrl;
  }

  public function isValid(): bool
  {
    return $this->valid;
  }

  public function getMessages(): array
  {
    return $this->messages;
  }

  public function getRedirectUrl(): ?string
  {
    return $this->redirectUrl;
  }

  public function setRedirectUrl(string $url): void
  {
    $this->redirectUrl = $url;
  }
}