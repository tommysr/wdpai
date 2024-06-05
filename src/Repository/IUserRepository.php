<?php

namespace App\Repository;
use App\Models\Interfaces\IUser;

interface IUserRepository
{
  public function addUser(IUser $user): void;
  public function getUser(string $email): ?IUser;
  public function getUserById(int $id): ?IUser;
  public function userExists(string $email): bool;
  public function userNameExists(string $username): bool;
  public function getAllUserIds(): array;
  public function getMaxUserId(): int;
}