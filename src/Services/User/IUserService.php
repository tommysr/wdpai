<?php
namespace App\Services\User;

use App\Models\Interfaces\IUser;

interface IUserService
{
    public function changePassword(int $userId, string $newPassword): void;
    public function verifyPassword(int $userId, string $password): bool;
    public function getUserById(int $userId): ?IUser;
}