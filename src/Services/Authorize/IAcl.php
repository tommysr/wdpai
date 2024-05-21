<?php

namespace App\Services\Authorize;

use App\Services\Authorize\IRole;

interface IAcl
{
  public function isAllowed(IRole $role, string $resource, string $action): bool;
  public function addRole(IRole $role): void;
  public function addResource(string $resource): void;
  public function addAction(string $action): void;
  public function allow(IRole $role, string $resource, string $action): void;
  public function deny(IRole $role, string $resource, string $action): void;
}