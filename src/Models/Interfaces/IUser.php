<?php

namespace App\Models\Interfaces;

use App\Models\Interfaces\IRole;

interface IUser
{
  public function getId(): int;
  public function getEmail(): string;
  public function getPassword(): string;
  public function getName(): string;
  public function getJoinDate(): string;
  public function getRole(): IRole;
  public function getAvatarUrl(): string;
  public function setPassword(string $password): void;
}