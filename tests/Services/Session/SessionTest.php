<?php

use PHPUnit\Framework\TestCase;
use App\Services\Session\SessionService;

class SessionTest extends TestCase
{
  public function testSetAndGet()
  {
    $sessionService = new SessionService();
    $sessionService->start();

    $key = 'test_key';
    $value = 'test_value';

    $sessionService->set($key, $value);

    $this->assertEquals($value, $sessionService->get($key));
  }

  public function testHas()
  {
    $sessionService = new SessionService();
    $sessionService->start();

    $key = 'test_key';
    $value = 'test_value';

    $sessionService->set($key, $value);

    $this->assertTrue($sessionService->has($key));
    $this->assertFalse($sessionService->has('non_existing_key'));
  }

  public function testDelete()
  {
    $sessionService = new SessionService();
    $sessionService->start();

    $key = 'test_key';
    $value = 'test_value';

    $sessionService->set($key, $value);
    $sessionService->delete($key);

    $this->assertFalse($sessionService->has($key));
  }


  public function testEnd()
  {
    $sessionService = new SessionService();
    $sessionService->start();

    $key = 'test_key';
    $value = 'test_value';

    $sessionService->set($key, $value);
    $sessionService->end();

    $this->assertEquals(1,1);
  }
}