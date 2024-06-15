<?php

use PHPUnit\Framework\TestCase;
use App\Emitter\Emitter;
use App\Middleware\IResponse;
use App\Middleware\IRedirectResponse;
use App\Middleware\IJsonResponse;

class EmitterTest extends TestCase
{
  public function testEmitNormalResponse()
  {
    // Create a mock IResponse object
    $response = $this->createMock(IResponse::class);
    $response->method('getHeaders')->willReturn(['Content-Type' => ['text/plain']]);
    $response->method('getStatusCode')->willReturn(200);
    $response->method('getBody')->willReturn('Hello, World!');

    // Create an instance of the Emitter class
    $emitter = new Emitter();

    // Capture the output of the emitNormalResponse method
    ob_start();
    $emitter->emit($response);
    $output = ob_get_clean();

    // Assert that the headers and body are correctly emitted
    $this->assertSame('Hello, World!', $output);
  }

  public function testEmitJsonResponse()
  {
    // Create a mock IJsonResponse object
    $response = $this->createMock(IJsonResponse::class);
    $response->method('getHeaders')->willReturn(['Content-Type' => ['application/json']]);
    $response->method('getStatusCode')->willReturn(200);
    $response->method('getData')->willReturn(['message' => 'Hello, World!']);

    // Create an instance of the Emitter class
    $emitter = new Emitter();

    // Capture the output of the emitJsonResponse method
    ob_start();
    $emitter->emit($response);
    $output = ob_get_clean();

    // Assert that the headers and JSON data are correctly emitted
    $this->assertSame('{"message":"Hello, World!"}', $output);
  }
}