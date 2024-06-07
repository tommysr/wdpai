<?php

namespace App\Repository;

use App\Models\Interfaces\IUser;

interface IUserRepository
{
  public function addUser(IUser $user): void;
  public function updateUser(IUser $user): void;
  
  public function getUserByEmail(string $email): ?IUser;
  public function getUserById(int $id): ?IUser;
  public function getUserByName(string $name): ?IUser;

  public function getAllUserIds(): array;
  public function getMaxUserId(): int;
}