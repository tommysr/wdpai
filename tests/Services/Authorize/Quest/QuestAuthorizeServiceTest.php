<?php

use App\Services\Authorize\Quest\IQuestAuthorizeStrategy;
use PHPUnit\Framework\TestCase;
use App\Services\Authorize\Quest\QuestAuthorizeService;
use App\Services\Authorize\Quest\IAuthorizationStrategyFactory;
use App\Services\Session\ISessionService;
use App\Services\Authorize\Quest\QuestRequest;
use App\Services\Authorize\Quest\IAuthResult;

class QuestAuthorizeServiceTest extends TestCase
{
  public function testAuthorizeQuest()
  {
    // Create mock objects for dependencies
    $strategyFactory = $this->createMock(IAuthorizationStrategyFactory::class);
    $strategy = $this->createMock(IQuestAuthorizeStrategy::class);
    $session = $this->createMock(ISessionService::class);

    // Set up expectations for the mock objects
    $request = QuestRequest::EDIT;
    $questId = 123;
    $authResult = $this->createMock(\App\Services\Authorize\IAuthResult::class);

    $strategyFactory->expects($this->once())
      ->method('create')
      ->with($request)
      ->willReturn($strategy);

    $strategy->expects($this->once())
      ->method('authorize')
      ->with($questId)
      ->willReturn($authResult);

    // Create an instance of the QuestAuthorizeService class
    $service = new QuestAuthorizeService($strategyFactory);

    // Call the method under test
    $result = $service->authorizeQuest($request, $questId);

    // Assert that the result matches the expected value
    $this->assertSame($authResult, $result);
  }
}