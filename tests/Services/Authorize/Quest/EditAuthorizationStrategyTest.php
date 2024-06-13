<?php

use App\Models\IQuest;
use App\Models\Quest;
use App\Models\Role;
use App\Models\UserRole;
use App\Repository\IQuestRepository;
use App\Services\Authenticate\IAuthService;
use App\Services\Authenticate\UserIdentity;
use App\Services\Authorize\AuthorizationResult;
use App\Services\Authorize\Quest\EditAuthorizationStrategy;
use PHPUnit\Framework\TestCase;

class EditAuthorizationStrategyTest extends TestCase
{
  public function testAuthorizeWithAdminRole()
  {
    $authServiceMock = $this->createMock(IAuthService::class);
    $questRepositoryMock = $this->createMock(IQuestRepository::class);

    $identity = new UserIdentity(1, new Role(UserRole::ADMIN->value, 1));

    $authServiceMock->expects($this->exactly(2))
      ->method('getIdentity')
      ->willReturn($identity);


      $quest = $this->createMock(IQuest::class);
      $quest->method('getCreatorId')
        ->willReturn(2);
  
      $quest->method('getIsApproved')
        ->willReturn(true);
  
      $questRepositoryMock->expects($this->once())
        ->method('getQuestById')
        ->willReturn($quest);

    $strategy = new EditAuthorizationStrategy($authServiceMock, $questRepositoryMock);
    $result = $strategy->authorize(1);

    $this->assertInstanceOf(AuthorizationResult::class, $result);
    $this->assertTrue($result->isValid());
    $this->assertEmpty($result->getMessages());
  }

  public function testAuthorizeWithQuestNotOwnedByUser()
  {
    $authServiceMock = $this->createMock(IAuthService::class);
    $questRepositoryMock = $this->createMock(IQuestRepository::class);

    $identity = new UserIdentity(1, new Role(UserRole::CREATOR->value, 1));

    $authServiceMock->expects($this->exactly(2))
      ->method('getIdentity')
      ->willReturn($identity);

    $quest = $this->createMock(IQuest::class);
    $quest->method('getCreatorId')
      ->willReturn(2);

    $quest->method('getIsApproved')
      ->willReturn(true);

    $questRepositoryMock->expects($this->once())
      ->method('getQuestById')
      ->willReturn($quest);


    $strategy = new EditAuthorizationStrategy($authServiceMock, $questRepositoryMock);
    $result = $strategy->authorize(1);

    $this->assertInstanceOf(AuthorizationResult::class, $result);
    $this->assertFalse($result->isValid());
    $this->assertEquals(['quest is not owned by you'], $result->getMessages());
  }

  public function testAuthorizeWithApprovedQuest()
  {
    $authServiceMock = $this->createMock(IAuthService::class);
    $questRepositoryMock = $this->createMock(IQuestRepository::class);
    $identity = new UserIdentity(1, new Role(UserRole::NORMAL->value, 1));

    $authServiceMock->expects($this->exactly(2))
      ->method('getIdentity')
      ->willReturn($identity);

    $quest = $this->createMock(IQuest::class);
    $quest->method('getCreatorId')
      ->willReturn(1);

    $quest->method('getIsApproved')
      ->willReturn(true);

    $questRepositoryMock->expects($this->once())
      ->method('getQuestById')
      ->willReturn($quest);

    $strategy = new EditAuthorizationStrategy($authServiceMock, $questRepositoryMock);
    $result = $strategy->authorize(1);

    $this->assertInstanceOf(AuthorizationResult::class, $result);
    $this->assertFalse($result->isValid());
    $this->assertEquals(['quest is already approved'], $result->getMessages());
  }

  public function testAuthorizeWithNonExistingQuest()
  {
    $authServiceMock = $this->createMock(IAuthService::class);
    $questRepositoryMock = $this->createMock(IQuestRepository::class);
    $identity = new UserIdentity(1, new Role(UserRole::NORMAL->value, 1));

    $authServiceMock->expects($this->once())
      ->method('getIdentity')
      ->willReturn($identity);


    $questRepositoryMock->expects($this->once())
      ->method('getQuestById')
      ->willReturn(null);

    $strategy = new EditAuthorizationStrategy($authServiceMock, $questRepositoryMock);
    $result = $strategy->authorize(1);

    $this->assertInstanceOf(AuthorizationResult::class, $result);
    $this->assertFalse($result->isValid());
    $this->assertEquals(['quest not found'], $result->getMessages());
  }
}