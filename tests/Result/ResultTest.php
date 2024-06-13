<?php

use PHPUnit\Framework\TestCase;
use App\Result\Result;

class ResultTest extends TestCase
{
  public function testGetMessages()
  {
    $messages = ['Message 1', 'Message 2'];
    $result = new Result($messages);

    $this->assertEquals($messages, $result->getMessages());
  }

  public function testIsValid()
  {
    $result = new Result([], true);

    $this->assertTrue($result->isValid());
  }
}