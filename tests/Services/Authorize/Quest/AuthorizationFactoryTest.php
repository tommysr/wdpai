<?php

use App\Services\Authorize\Quest\EditAuthorizationStrategy;
use App\Services\Authorize\Quest\PlayAuthorizationStrategy;
use PHPUnit\Framework\TestCase;
use App\Services\Authorize\Quest\AuthorizationFactory;
use App\Services\Authorize\Quest\QuestRequest;
use App\Services\Authenticate\IAuthService;
use App\Services\Session\ISessionService;
use App\Repository\IQuestRepository;
use App\Repository\QuestProgress\IQuestProgressRepository;

class AuthorizationFactoryTest extends TestCase
{
  public function testCreateAccessStrategy()
  {
    // Mock dependencies
    $session = $this->createMock(ISessionService::class);
    $authService = $this->createMock(IAuthService::class);
    $questProgress = $this->createMock(IQuestProgressRepository::class);
    $questRepository = $this->createMock(IQuestRepository::class);

    // Create the factory instance
    $factory = new AuthorizationFactory($session, $authService, $questProgress, $questRepository);

    // Create a QuestRequest instance for access
    $request = QuestRequest::ACCESS;

    // Call the create method and assert the returned strategy is of the correct type
    $strategy = $factory->create($request);
    $this->assertInstanceOf(PlayAuthorizationStrategy::class, $strategy);
  }

  public function testCreateEditStrategy()
  {
    // Mock dependencies
    $authService = $this->createMock(IAuthService::class);
    $questRepository = $this->createMock(IQuestRepository::class);
    $questProgressRepository = $this->createMock(IQuestProgressRepository::class);
    $sessionService = $this->createMock(ISessionService::class);

    // Create the factory instance
    $factory = new AuthorizationFactory($sessionService, $authService, $questProgressRepository, $questRepository);

    // Create a QuestRequest instance for edit
    $request = QuestRequest::EDIT;

    // Call the create method and assert the returned strategy is of the correct type
    $strategy = $factory->create($request);
    $this->assertInstanceOf(EditAuthorizationStrategy::class, $strategy);
  }
}