<?php

namespace App\Middleware;

use App\Middleware\IRedirectResponse;
use App\Middleware\BaseResponse;

class RedirectResponse extends BaseResponse implements IRedirectResponse
{
  private string $redirectUri;
  private array $messages = [];

  public function __construct(string $redirectUri, array $messages = [], int $statusCode = 303)
  {
    parent::__construct($statusCode);
    $this->redirectUri = $redirectUri;
    $this->messages = $messages;
  }

  public function getRedirectUri(): string
  {
    return $this->redirectUri;
  }

  public function getMessages(): array
  {
    return $this->messages;
  }
}