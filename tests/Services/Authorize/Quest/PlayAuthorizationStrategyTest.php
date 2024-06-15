<?php

use App\Models\Interfaces\IQuestProgress;
use App\Models\IQuest;
use App\Models\Role;
use App\Models\UserRole;
use App\Services\Authenticate\IIdentity;
use App\Services\Authenticate\UserIdentity;
use PHPUnit\Framework\TestCase;
use App\Services\Authorize\Quest\PlayAuthorizationStrategy;
use App\Services\Authenticate\IAuthService;
use App\Services\Session\ISessionService;
use App\Repository\IQuestRepository;
use App\Repository\QuestProgress\IQuestProgressRepository;

class PlayAuthorizationStrategyTest extends TestCase
{
  private $authService;
  private $sessionService;
  private $questProgressRepository;
  private $questRepository;

  protected function setUp(): void
  {
    $this->authService = $this->createMock(IAuthService::class);
    $this->sessionService = $this->createMock(ISessionService::class);
    $this->questProgressRepository = $this->createMock(IQuestProgressRepository::class);
    $this->questRepository = $this->createMock(IQuestRepository::class);
  }

  public function testAuthorizeWithInProgressGameplay()
  {
    $questProgressMock = $this->createMock(IQuestProgress::class);

    $identity = new UserIdentity(1, new Role(UserRole::NORMAL->value, 1));

    $this->authService->expects($this->once())
      ->method('getIdentity')
      ->willReturn($identity);

    $this->sessionService->expects($this->once())
      ->method('get')
      ->with('questProgress')
      ->willReturn($questProgressMock);

    $strategy = new PlayAuthorizationStrategy(
      $this->authService,
      $this->sessionService,
      $this->questProgressRepository,
      $this->questRepository
    );

    $result = $strategy->authorize();

    $this->assertEquals(['you have a gameplay in progress'], $result->getMessages());
    $this->assertEquals('/play', $result->getRedirectUrl());
    $this->assertFalse($result->isValid());
  }

  public function testAuthorizeWithQuestInProgress()
  {
    $identity = new UserIdentity(1, new Role(UserRole::NORMAL->value, 1));
    $questProgressMock = $this->createMock(IQuestProgress::class);



    $this->authService->expects($this->once())
      ->method('getIdentity')
      ->willReturn($identity);

    $this->sessionService->expects($this->once())
      ->method('get')
      ->with('questProgress')
      ->willReturn(null);

    $this->questProgressRepository->expects($this->once())
      ->method('getInProgress')
      ->willReturn($questProgressMock);

    $this->sessionService->expects($this->once())
      ->method('set')
      ->with('questProgress', $questProgressMock);

    $strategy = new PlayAuthorizationStrategy(
      $this->authService,
      $this->sessionService,
      $this->questProgressRepository,
      $this->questRepository
    );

    $result = $strategy->authorize();

    $this->assertEquals(['you have a gameplay in progress'], $result->getMessages());
    $this->assertEquals('/play', $result->getRedirectUrl());
    $this->assertFalse($result->isValid());
  }

  public function testAuthorizeWithQuestIdAndExistingProgress()
  {
    $questProgressMock = $this->createMock(IQuestProgress::class);
    $quest = $this->createMock(IQuest::class);

    $identity = new UserIdentity(1, new Role(UserRole::NORMAL->value, 1));

    $this->authService->expects($this->once())
      ->method('getIdentity')
      ->willReturn($identity);

    $this->questProgressRepository->expects($this->once())
      ->method('getQuestProgress')
      ->willReturn($questProgressMock);

    $this->questRepository->expects($this->once())
      ->method('getQuestById')
      ->willReturn($quest);

    $strategy = new PlayAuthorizationStrategy(
      $this->authService,
      $this->sessionService,
      $this->questProgressRepository,
      $this->questRepository
    );

    $result = $strategy->authorize(1);

    $this->assertEquals(['you have already played this quest'], $result->getMessages());
    $this->assertFalse($result->isValid());
  }

  public function testAuthorizeWithQuestIdAndNonexistentQuest()
  {
    $this->authService->expects($this->once())
      ->method('getIdentity')
      ->willReturn($this->createMock(IIdentity::class));

    $this->questRepository->expects($this->once())
      ->method('getQuestById')
      ->willReturn(null);

    $strategy = new PlayAuthorizationStrategy(
      $this->authService,
      $this->sessionService,
      $this->questProgressRepository,
      $this->questRepository
    );

    $result = $strategy->authorize(1);

    $this->assertEquals(['quest not found'], $result->getMessages());
    $this->assertFalse($result->isValid());
  }

  public function testAuthorizeWithoutQuestId()
  {
    $this->authService->expects($this->once())
      ->method('getIdentity')
      ->willReturn($this->createMock(IIdentity::class));

    $strategy = new PlayAuthorizationStrategy(
      $this->authService,
      $this->sessionService,
      $this->questProgressRepository,
      $this->questRepository
    );

    $result = $strategy->authorize();

    $this->assertEquals([], $result->getMessages());
    $this->assertTrue($result->isValid());
  }
}