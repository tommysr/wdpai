<?php

use PHPUnit\Framework\TestCase;
use App\Services\Authorize\AuthorizationResult;

class AuthorizationResultTest extends TestCase
{
  public function testIsValid()
  {
    $result = new AuthorizationResult([], true);
    $this->assertTrue($result->isValid());

    $result = new AuthorizationResult([], false);
    $this->assertFalse($result->isValid());
  }

  public function testGetMessages()
  {
    $messages = ['Message 1', 'Message 2'];
    $result = new AuthorizationResult($messages);
    $this->assertEquals($messages, $result->getMessages());
  }

  public function testGetRedirectUrl()
  {
    $url = 'https://example.com';
    $result = new AuthorizationResult([], true, $url);
    $this->assertEquals($url, $result->getRedirectUrl());
  }

  public function testSetRedirectUrl()
  {
    $url = 'https://example.com';
    $result = new AuthorizationResult();
    $result->setRedirectUrl($url);
    $this->assertEquals($url, $result->getRedirectUrl());
  }
}