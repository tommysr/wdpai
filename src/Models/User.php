<?php

namespace App\Models;

use App\Models\Interfaces\IRole;
use App\Models\Role;
use DateTime;
use App\Models\Interfaces\IUser;


class User implements IUser
{
  private string $email;
  private string $password;
  private string $name;
  private string $joinDate;
  private string $avatarUrl = '';
  private int $id;
  private IRole $role;

  public function __construct(
    int $id,
    string $email,
    string $password,
    string $name,
    IRole $role,
    string $joinDate = null,
    string $avatarUrl = ''
  ) {
    $this->id = $id;
    $this->email = $email;
    $this->password = $password;
    $this->name = $name;
    $this->role = $role;
    $this->avatarUrl = $avatarUrl;

    if ($joinDate !== null) {
      $this->joinDate = DateTime::createFromFormat('Y-m-d', $joinDate)->format('Y-m-d');
    } else {
      $this->joinDate = date('Y-m-d');
    }
  }

  public function setPassword(string $password): void
  {
    $this->password = $password;
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function getEmail(): string
  {
    return $this->email;
  }

  public function getPassword(): string
  {
    return $this->password;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function getJoinDate(): string
  {
    return $this->joinDate;
  }

  public function getRole(): IRole
  {
    return $this->role;
  }

  public function getAvatarUrl(): string
  {
    return $this->avatarUrl;
  }
}