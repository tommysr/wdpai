<?php

namespace App\Services\Authenticate;

use App\Services\Authenticate\IIdentity;
use App\Models\Interfaces\IRole;
use App\Models\Role;

class UserIdentity implements IIdentity
{
  private IRole $role;
  private int $id;

  public function __construct(int $id, IRole $role)
  {
    $this->id = $id;
    $this->role = $role;
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function getRole(): IRole
  {
    return $this->role;
  }

  public function toString(): string
  {
    return $this->id . ':' . $this->role->getName();
  }

  public static function fromString(string $identity): IIdentity
  {
    $parts = explode(':', $identity);
    $role = Role::fromName($parts[1]);
    return new UserIdentity((int) $parts[0], $role);
  }
}