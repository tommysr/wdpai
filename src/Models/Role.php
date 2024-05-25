<?php

namespace App\Models;

use App\Models\Interfaces\IRole;

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

  public static function fromName(string $role): self
  {
    return new Role($role);
  }
}