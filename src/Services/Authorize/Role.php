<?php

namespace App\Services\Authorize;
use App\Services\Authorize\IRole;

class Role implements IRole
{
  private string $role;

  public function __construct(string $role)
  {
    $this->role = $role;
  }

  public function getName(): string
  {
    return $this->role;
  }

  public static function fromName(string $role): IRole
  {
    return new Role($role);
  }
}