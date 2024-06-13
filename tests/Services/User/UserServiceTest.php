<?php

use App\Models\Interfaces\IRole;
use PHPUnit\Framework\TestCase;
use App\Services\User\UserService;
use App\Models\Interfaces\IUser;
use App\Repository\IUserRepository;
use App\Repository\Role\IRoleRepository;

class UserServiceTest extends TestCase
{
  private $userRepository;
  private $roleRepository;
  private $userService;

  protected function setUp(): void
  {
    $this->userRepository = $this->createMock(IUserRepository::class);
    $this->roleRepository = $this->createMock(IRoleRepository::class);
    $this->userService = new UserService($this->userRepository, $this->roleRepository);
  }

  public function testGetUserById()
  {
    $userId = 1;
    $user = $this->createMock(IUser::class);
    $this->userRepository->expects($this->once())
      ->method('getUserById')
      ->with($userId)
      ->willReturn($user);

    $result = $this->userService->getUserById($userId);

    $this->assertSame($user, $result);
  }

  // public function testChangePassword()
  // {
  //     $userId = 1;
  //     $newPassword = 'newpassword';
  //     $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT, ['cost' => 12]); // Example cost

  //     // Mock User object
  //     $user = $this->createMock(IUser::class);
  //     $user->expects($this->once())
  //          ->method('setPassword')
  //          ->with($this->equalTo($hashedPassword)); // Ensure the exact hashed password is expected

  //     // Mock UserRepository
  //     $this->userRepository->expects($this->once())
  //                          ->method('getUserById')
  //                          ->with($userId)
  //                          ->willReturn($user);

  //     $this->userRepository->expects($this->once())
  //                          ->method('updateUser')
  //                          ->with($user);

  //     // Call method under test
  //     $this->userService->changePassword($userId, $newPassword);
  // }

  public function testVerifyPassword()
  {
    $userId = 1;
    $password = 'password';
    $user = $this->createMock(IUser::class);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $this->userRepository->expects($this->once())
      ->method('getUserById')
      ->with($userId)
      ->willReturn($user);

    $user->expects($this->once())
      ->method('getPassword')
      ->willReturn($hashedPassword);

    $result = $this->userService->verifyPassword($userId, $password);

    $this->assertTrue($result);
  }

  public function testPromoteToCreator()
  {
    $role = $this->createMock(IRole::class);
    $name = 'John Doe';
    $roleName = 'creator';
    $user = $this->createMock(IUser::class);

    $this->userRepository->expects($this->once())
      ->method('getUserByName')
      ->with($name)
      ->willReturn($user);

    $this->roleRepository->expects($this->once())
      ->method('getRole')
      ->with($roleName)
      ->willReturn($role);

    $user->expects($this->once())
      ->method('setRole')
      ->with($role);

    $this->userRepository->expects($this->once())
      ->method('updateUser')
      ->with($user);

    $this->userService->promoteToCreator($name);
  }
}