<?php

namespace App\Services\Authorize;


/**
 * Interface for authorizing quests.
 */
interface IAuthResult
{
  /**
   * Check if the result is valid.
   * 
   * @return bool True if the result is valid, false otherwise.
   */
  public function isValid(): bool;

  /**
   * Get the identity of the result.
   * 
   * @return string The identity of the result.
   */
  public function getIdentity(): string;
}