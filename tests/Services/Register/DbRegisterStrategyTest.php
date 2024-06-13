<?php

use App\Models\Interfaces\IRole;
use App\Models\Role;
use PHPUnit\Framework\TestCase;
use App\Services\Register\DbRegisterStrategy;
use App\Repository\IUserRepository;
use App\Repository\Role\IRoleRepository;
use App\Request\IFullRequest;
use App\Models\User;
use App\Models\UserRole;
use App\Result\Result;

class DbRegisterStrategyTest extends TestCase
{
  private $userRepository;
  private $roleRepository;
  private $request;
  private $registerStrategy;

  protected function setUp(): void
  {
    $this->userRepository = $this->createMock(IUserRepository::class);
    $this->roleRepository = $this->createMock(IRoleRepository::class);
    $this->request = $this->createMock(IFullRequest::class);
    $this->registerStrategy = new DbRegisterStrategy($this->request, $this->userRepository, $this->roleRepository);
  }

  public function testRegisterEmailExists()
  {
    $this->request->method('getParsedBodyParam')->willReturnMap([
      ['email', null, 'test@example.com'],
      ['username', null, 'testuser'],
      ['password', null, 'password123'],
      ['confirmedPassword', null, 'password123']
    ]);

    $this->userRepository->method('getUserByEmail')->willReturn(new User(1, 'test@example.com', 'password_hash', 'testuser', new Role(UserRole::NORMAL->value, 1)));

    $result = $this->registerStrategy->register();

    $this->assertFalse($result->isValid());
    $this->assertEquals(['Email exists'], $result->getMessages());
  }

  public function testRegisterUsernameExists()
  {
    $this->request->method('getParsedBodyParam')->willReturnMap([
      ['email', null, 'test@example.com'],
      ['username', null, 'testuser'],
      ['password', null, 'password123'],
      ['confirmedPassword', null, 'password123']
    ]);

    $this->userRepository->method('getUserByName')->willReturn(new User(1, 'test@example.com', 'password_hash', 'testuser', new Role(UserRole::NORMAL->value, 1)));

    $result = $this->registerStrategy->register();

    $this->assertFalse($result->isValid());
    $this->assertEquals(['Username already taken'], $result->getMessages());
  }

  public function testRegisterPasswordsDoNotMatch()
  {
    $this->request->method('getParsedBodyParam')->willReturnMap([
      ['email', null, 'test@example.com'],
      ['username', null, 'testuser'],
      ['password', null, 'password123'],
      ['confirmedPassword', null, 'differentPassword']
    ]);

    $result = $this->registerStrategy->register();

    $this->assertFalse($result->isValid());
    $this->assertEquals(['Passwords do not match'], $result->getMessages());
  }

  public function testRegisterSuccess()
  {
    $this->request->method('getParsedBodyParam')->willReturnMap([
      ['email', null, 'test@example.com'],
      ['username', null, 'testuser'],
      ['password', null, 'password123'],
      ['confirmedPassword', null, 'password123']
    ]);

    $this->userRepository->method('getUserByEmail')->willReturn(null);
    $this->userRepository->method('getUserByName')->willReturn(null);

    $role = $this->createMock(IRole::class);
    $this->roleRepository->method('getRole')->willReturn($role);

    $this->userRepository->expects($this->once())->method('addUser')->with($this->callback(function ($user) {
      return $user instanceof User && $user->getEmail() === 'test@example.com' && $user->getName() === 'testuser';
    }));

    $result = $this->registerStrategy->register();

    $this->assertTrue($result->isValid());
    $this->assertEquals(['User registered successfully'], $result->getMessages());
  }
}
