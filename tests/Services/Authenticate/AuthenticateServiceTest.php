<?php

use PHPUnit\Framework\TestCase;
use App\Services\Authenticate\AuthenticateService;
use App\Services\Session\ISessionService;
use App\Services\Authenticate\IAuthAdapter;
use App\Services\Authenticate\IAuthResult;
use App\Services\Authenticate\IIdentity;

class AuthenticateServiceTest extends TestCase
{
  public function testAuthenticate_ValidResult_SaveIdentity()
  {
    // Arrange
    $sessionMock = $this->createMock(ISessionService::class);
    $adapterMock = $this->createMock(IAuthAdapter::class);
    $resultMock = $this->createMock(IAuthResult::class);
    $identityMock = $this->createMock(IIdentity::class);

    $adapterMock->expects($this->once())
      ->method('authenticate')
      ->willReturn($resultMock);

    $resultMock->expects($this->once())
      ->method('isValid')
      ->willReturn(true);

    $resultMock->expects($this->once())
      ->method('getIdentity')
      ->willReturn($identityMock);

    $sessionMock->expects($this->once())
      ->method('set')
      ->with('identity', $identityMock->toString());

    $service = new AuthenticateService($sessionMock);

    // Act
    $result = $service->authenticate($adapterMock);

    // Assert
    $this->assertSame($resultMock, $result);
  }

  public function testAuthenticate_InvalidResult_DoNotSaveIdentity()
  {
    // Arrange
    $sessionMock = $this->createMock(ISessionService::class);
    $adapterMock = $this->createMock(IAuthAdapter::class);
    $resultMock = $this->createMock(IAuthResult::class);

    $adapterMock->expects($this->once())
      ->method('authenticate')
      ->willReturn($resultMock);

    $resultMock->expects($this->once())
      ->method('isValid')
      ->willReturn(false);

    $sessionMock->expects($this->never())
      ->method('set');

    $service = new AuthenticateService($sessionMock);

    // Act
    $result = $service->authenticate($adapterMock);

    // Assert
    $this->assertSame($resultMock, $result);
  }
}