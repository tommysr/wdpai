<?php

use App\Services\Register\IRegisterStrategy;
use PHPUnit\Framework\TestCase;
use App\Services\Register\RegisterService;
use App\Request\IFullRequest;
use App\Result\IResult;
use App\Services\Register\IRegisterStrategyFactory;

class RegisterServiceTest extends TestCase
{
  public function testRegister()
  {
    // Create mock objects for dependencies
    $request = $this->createMock(IFullRequest::class);
    $strategyFactory = $this->createMock(IRegisterStrategyFactory::class);
    $strategy = $this->createMock(IRegisterStrategy::class);
    $result = $this->createMock(IResult::class);

    // Set up expectations for the mock objects
    $method = 'some_registration_method';
    $request->expects($this->once())
      ->method('getParsedBodyParam')
      ->with('registration_method')
      ->willReturn($method);

    $strategyFactory->expects($this->once())
      ->method('create')
      ->with($method)
      ->willReturn($strategy);

    $strategy->expects($this->once())
      ->method('register')
      ->willReturn($result);

    // Create an instance of the RegisterService class
    $registerService = new RegisterService($request, $strategyFactory);

    // Call the register method and assert the result
    $this->assertSame($result, $registerService->register());
  }
}