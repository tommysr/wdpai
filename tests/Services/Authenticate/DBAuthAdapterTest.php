<?php

use App\Models\Role;
use App\Models\UserRole;
use PHPUnit\Framework\TestCase;
use App\Services\Authenticate\DBAuthAdapter;
use App\Repository\IUserRepository;
use App\Models\User;
use App\Services\Authenticate\DBAuthResult;

class DBAuthAdapterTest extends TestCase
{
  private $userRepository;
  private $authAdapter;

  protected function setUp(): void
  {
    $this->userRepository = $this->createMock(IUserRepository::class);
  }

  public function testAuthenticateWithInvalidEmail()
  {
    $this->userRepository->method('getUserByEmail')->willReturn(null);

    $this->authAdapter = new DBAuthAdapter('invalid@example.com', 'password123', $this->userRepository);
    $result = $this->authAdapter->authenticate();

    $this->assertInstanceOf(DBAuthResult::class, $result);
    $this->assertNull($result->getIdentity());
    $this->assertEquals(['invalid credentials'], $result->getMessages());
  }

  public function testAuthenticateWithIncorrectPassword()
  {
    $user = new User(1, 'test@example.com', password_hash('password123', PASSWORD_DEFAULT), 'testuser', new Role(UserRole::NORMAL->value, 1));

    $this->userRepository->method('getUserByEmail')->willReturn($user);

    $this->authAdapter = new DBAuthAdapter('test@example.com', 'wrongpassword', $this->userRepository);
    $result = $this->authAdapter->authenticate();

    $this->assertInstanceOf(DBAuthResult::class, $result);
    $this->assertNull($result->getIdentity());
    $this->assertEquals(['invalid credentials'], $result->getMessages());
  }

  public function testAuthenticateSuccessfully()
  {
    $user = new User(1, 'test@example.com', password_hash('password123', PASSWORD_DEFAULT), 'testuser', new Role(UserRole::NORMAL->value, 1));

    $this->userRepository->method('getUserByEmail')->willReturn($user);

    $this->authAdapter = new DBAuthAdapter('test@example.com', 'password123', $this->userRepository);
    $result = $this->authAdapter->authenticate();

    $this->assertInstanceOf(DBAuthResult::class, $result);
    $this->assertNotNull($result->getIdentity());
    $this->assertEquals(['Authenticated'], $result->getMessages());
    $this->assertTrue($result->isValid());
  }
}

