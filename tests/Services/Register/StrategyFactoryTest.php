<?php

use App\Services\Register\IRegisterStrategy;
use PHPUnit\Framework\TestCase;
use App\Services\Register\StrategyFactory;
use App\Services\Register\InvalidRegisterMethodException;

class StrategyFactoryTest extends TestCase
{
  public function testRegisterStrategy()
  {
    $factory = new StrategyFactory();
    $strategy = $this->createMock(IRegisterStrategy::class);

    $factory->registerStrategy('method', $strategy);

    $this->assertArrayHasKey('method', $factory->getStrategies());
    $this->assertSame($strategy, $factory->getStrategies()['method']);
  }

  public function testCreateWithValidMethod()
  {
    $factory = new StrategyFactory();
    $strategy = $this->createMock(IRegisterStrategy::class);
    $factory->registerStrategy('method', $strategy);

    $createdStrategy = $factory->create('method');

    $this->assertSame($strategy, $createdStrategy);
  }

  public function testCreateWithInvalidMethod()
  {
    $factory = new StrategyFactory();

    $this->expectException(InvalidRegisterMethodException::class);
    $factory->create('invalidMethod');
  }
}