<?php

namespace App\Services\Authorize;


/**
 * Interface for authorizing quests.
 */
interface IAuthResult
{
  public function isValid(): bool;
  public function getMessages(): array;
  public function getRedirectUrl(): ?string;
  public function setRedirectUrl(string $url): void;
}