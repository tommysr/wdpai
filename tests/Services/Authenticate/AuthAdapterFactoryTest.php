<?php

use App\Repository\IUserRepository;
use App\Request\IFullRequest;
use App\Services\Authenticate\AuthAdapterFactory;
use App\Services\Authenticate\DBAuthAdapter;
use PHPUnit\Framework\TestCase;

class AuthAdapterFactoryTest extends TestCase
{
  public function testCreateAuthAdapterWithValidRequest()
  {
    // Create a mock UserRepository
    $userRepository = $this->createMock(IUserRepository::class);

    // Create a mock FullRequest
    $request = $this->createMock(IFullRequest::class);
    $request->method('getParsedBody')->willReturn(['email' => 'test@example.com', 'password' => 'password']);

    $email = 'test@example.com';
    $password = 'password';
    $matcher = $this->exactly(2);

    $request->expects($this->exactly(2))->method('getParsedBodyParam')
      ->willReturnOnConsecutiveCalls('test@example.com', 'password');
    // Create an instance of AuthAdapterFactory
    $authAdapterFactory = new AuthAdapterFactory($userRepository);

    // Call the createAuthAdapter method
    $authAdapter = $authAdapterFactory->createAuthAdapter($request);

    // Assert that the returned value is an instance of DBAuthAdapter
    $this->assertInstanceOf(DBAuthAdapter::class, $authAdapter);
  }

  public function testCreateAuthAdapterWithInvalidRequest()
  {
    // Create a mock UserRepository
    $userRepository = $this->createMock(IUserRepository::class);

    // Create a mock FullRequest
    $request = $this->createMock(IFullRequest::class);
    $request->method('getParsedBody')->willReturn([]);
    $request->method('getParsedBodyParam')->willReturn(null);

    // Create an instance of AuthAdapterFactory
    $authAdapterFactory = new AuthAdapterFactory($userRepository);

    // Call the createAuthAdapter method
    $authAdapter = $authAdapterFactory->createAuthAdapter($request);

    // Assert that the returned value is null
    $this->assertNull($authAdapter);
  }
}