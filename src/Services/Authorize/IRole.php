<?php

namespace App\Services\Authorize;

interface IRole
{
  public function getName(): string;
  public static function fromName(string $role): IRole;
}