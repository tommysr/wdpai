<?php

namespace App\Services\Authenticate;
use App\Services\Authorize\IRole;

interface IIdentity {
  public function getId(): int;
  public function getRole(): IRole;
  public function toString(): string;
  public static function fromString(string $identity): IIdentity;
}