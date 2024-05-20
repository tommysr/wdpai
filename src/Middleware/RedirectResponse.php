<?php

namespace App\Middleware;

use App\Middleware\IRedirectResponse;
use App\Middleware\BaseResponse;

class RedirectResponse extends BaseResponse implements IRedirectResponse
{
  private string $redirectUri;

  public function __construct(string $redirectUri)
  {
    $this->redirectUri = $redirectUri;
  }

  public function getRedirectUri(): string
  {
    return $this->redirectUri;
  }
}