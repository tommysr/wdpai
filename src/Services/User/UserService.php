<?php
namespace App\Services\User;

use App\Models\Interfaces\IUser;
use App\Models\UserRole;
use App\Repository\IUserRepository;
use App\Repository\Role\IRoleRepository;
use App\Repository\UserRepository;
use App\Services\User\IUserService;

class UserService implements IUserService
{
    private IUserRepository $userRepository;
    private IRoleRepository $roleRepository;

    public function __construct(IUserRepository $userRepository, IRoleRepository $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    public function getUserById(int $userId): ?IUser
    {
        return $this->userRepository->getUserById($userId);
    }

    public function changePassword(int $userId, string $newPassword): void
    {
        $user = $this->userRepository->getUserById($userId);
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $user->setPassword($hashedPassword);
        $this->userRepository->updateUser($user);
    }

    public function verifyPassword(int $userId, string $password): bool
    {
        $user = $this->userRepository->getUserById($userId);
        return password_verify($password, $user->getPassword());
    }

    public function promoteToCreator(string $name): void
    {
        $user = $this->userRepository->getUserByName($name);
        $role = $this->roleRepository->getRole(UserRole::CREATOR->value);
        $user->setRole($role);
        $this->userRepository->updateUser($user);
    }
}