<?php

namespace App\Services\Authorize;

use App\Models\Interfaces\IRole;

interface IAcl
{
  public function isAllowed(string $role, string $resource, string $action): bool;
  public function addRole(string $role): void;
  public function addResource(string $resource): void;
  public function addAction(string $action): void;
  public function allow(string $role, string $resource, string $action): void;
  public function deny(string $role, string $resource, string $action): void;
}