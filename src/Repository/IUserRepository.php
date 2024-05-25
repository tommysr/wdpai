<?php

namespace App\Repository;
use App\Models\User;

interface IUserRepository
{
  public function addUser(User $user): void;
  public function getUser(string $email): ?User;
  public function getUserById(int $id): ?User;
  public function userExists($email): bool;
  public function userNameExists($username): bool;
  public function getAllUserIds(): array;
}