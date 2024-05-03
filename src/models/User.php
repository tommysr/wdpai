<?php

class User
{
  private string $email;
  private string $password;
  private string $name;
  private string $joinDate;
  private int $id;
  private string $role;

  public function __construct(
    int $id,
    string $email,
    string $password,
    string $name,
    string $role = 'normal',
    string $joinDate = null
  ) {
    $this->id = $id;
    $this->email = $email;
    $this->password = $password;
    $this->name = $name;
    $this->role = $role;

    if ($joinDate !== null) {
      $this->joinDate = DateTime::createFromFormat('Y-m-d', $joinDate)->format('Y-m-d');
    } else {
      $this->joinDate = date('Y-m-d');
    }
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

  public function getRole(): string
  {
    return $this->role;
  }
}