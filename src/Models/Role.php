<?php

namespace App\Models;

use App\Models\Interfaces\IRole;

class Role implements IRole
{
  private string $role;
  private int $role_id;

  public function __construct(string $role, int $role_id)
  {
    $this->role = $role;
    $this->role_id = $role_id;
  }

  public function getId(): int
  {
    return $this->role_id;
  }

  public function getName(): string
  {
    return $this->role;
  }

}