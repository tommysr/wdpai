<?php

class User
{
  private $email;
  private $password;
  private $name;
  private $joinDate;

  public function __construct(
    string $email,
    string $password,
    string $name,
    ?string $joinDate = null
  ) {
    $this->email = $email;
    $this->password = $password;
    $this->name = $name;
    $this->joinDate = $joinDate;

    if ($joinDate !== null) {
      $this->joinDate = DateTime::createFromFormat('Y-m-d', $joinDate)->format('Y-m-d');
    } else {
      $this->joinDate = date('Y-m-d');
    }
  }

  public function getEmail(): string
  {
    return $this->email;
  }

  public function getPassword()
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
}