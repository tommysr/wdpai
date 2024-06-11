<?php

namespace App\Repository\Role;

use App\Models\Interfaces\IRole;

interface IRoleRepository
{
  public function getRole(string $role): IRole;
  public function getRoles(): array;
}