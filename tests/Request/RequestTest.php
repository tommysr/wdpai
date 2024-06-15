<?php

use App\Request\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
  public function testGetPath()
  {
    $request = new Request(['REQUEST_URI' => '/example']);
    $this->assertEquals('/example', $request->getPath());
  }

  public function testGetMethod()
  {
    $request = new Request(['REQUEST_METHOD' => 'GET']);
    $this->assertEquals('GET', $request->getMethod());
  }

  public function testWithAttribute()
  {
    $request = new Request();
    $request->withAttribute('key', 'value');
    $this->assertEquals('value', $request->getAttribute('key'));
  }


  public function testGetAttributes()
  {
    $request = new Request();
    $request->withAttribute('key1', 'value1');
    $request->withAttribute('key2', 'value2');
    $attributes = $request->getAttributes();
    $this->assertArrayHasKey('key1', $attributes);
    $this->assertArrayHasKey('key2', $attributes);
    $this->assertEquals('value1', $attributes['key1']);
    $this->assertEquals('value2', $attributes['key2']);
  }

  public function testGetBody()
  {
    $rawBody = 'This is the raw body';
    $request = new Request([], [], [], [], [], $rawBody);
    $this->assertEquals($rawBody, $request->getBody());
  }

  public function testGetQuery()
  {
    $query = ['key1' => 'value1', 'key2' => 'value2'];
    $request = new Request([], $query);
    $this->assertEquals('value1', $request->getQuery('key1'));
    $this->assertEquals('value2', $request->getQuery('key2'));
    $this->assertNull($request->getQuery('key3'));
    $this->assertEquals('default', $request->getQuery('key3', 'default'));
  }

  public function testGetParsedBodyParam()
  {
    $body = ['key1' => 'value1', 'key2' => 'value2'];
    $request = new Request([], [], $body);
    $this->assertEquals('value1', $request->getParsedBodyParam('key1'));
    $this->assertEquals('value2', $request->getParsedBodyParam('key2'));
    $this->assertNull($request->getParsedBodyParam('key3'));
    $this->assertEquals('default', $request->getParsedBodyParam('key3', 'default'));
  }

  public function testGetCookie()
  {
    $cookies = ['key1' => 'value1', 'key2' => 'value2'];
    $request = new Request([], [], [], $cookies);
    $this->assertEquals('value1', $request->getCookie('key1'));
    $this->assertEquals('value2', $request->getCookie('key2'));
    $this->assertNull($request->getCookie('key3'));
    $this->assertEquals('default', $request->getCookie('key3', 'default'));
  }

  public function testGetServerParam()
  {
    $server = ['key1' => 'value1', 'key2' => 'value2'];
    $request = new Request($server);
    $this->assertEquals('value1', $request->getServerParam('key1'));
    $this->assertEquals('value2', $request->getServerParam('key2'));
    $this->assertNull($request->getServerParam('key3'));
    $this->assertEquals('default', $request->getServerParam('key3', 'default'));
  }

  public function testGetServerParams()
  {
    $server = ['key1' => 'value1', 'key2' => 'value2'];
    $request = new Request($server);
    $serverParams = $request->getServerParams();
    $this->assertArrayHasKey('key1', $serverParams);
    $this->assertArrayHasKey('key2', $serverParams);
    $this->assertEquals('value1', $serverParams['key1']);
    $this->assertEquals('value2', $serverParams['key2']);
  }

  public function testGetCookieParams()
  {
    $cookies = ['key1' => 'value1', 'key2' => 'value2'];
    $request = new Request([], [], [], $cookies);
    $cookieParams = $request->getCookieParams();
    $this->assertArrayHasKey('key1', $cookieParams);
    $this->assertArrayHasKey('key2', $cookieParams);
    $this->assertEquals('value1', $cookieParams['key1']);
    $this->assertEquals('value2', $cookieParams['key2']);
  }

  public function testGetQueryParams()
  {
    $query = ['key1' => 'value1', 'key2' => 'value2'];
    $request = new Request([], $query);
    $queryParams = $request->getQueryParams();
    $this->assertArrayHasKey('key1', $queryParams);
    $this->assertArrayHasKey('key2', $queryParams);
    $this->assertEquals('value1', $queryParams['key1']);
    $this->assertEquals('value2', $queryParams['key2']);
  }

  public function testGetUploadedFiles()
  {
    $files = ['file1' => 'file1.txt', 'file2' => 'file2.txt'];
    $request = new Request([], [], [], [], $files);
    $uploadedFiles = $request->getUploadedFiles();
    $this->assertArrayHasKey('file1', $uploadedFiles);
    $this->assertArrayHasKey('file2', $uploadedFiles);
    $this->assertEquals('file1.txt', $uploadedFiles['file1']);
    $this->assertEquals('file2.txt', $uploadedFiles['file2']);
  }

  public function testGetHeaders()
  {
    $server = [
      'HTTP_CONTENT_TYPE' => 'application/json',
      'HTTP_ACCEPT' => 'application/json',
      'HTTP_X_CUSTOM_HEADER' => 'custom value'
    ];
    $request = new Request($server);
    $headers = $request->getHeaders();
    $this->assertArrayHasKey('CONTENT_TYPE', $headers);
    $this->assertArrayHasKey('ACCEPT', $headers);
    $this->assertArrayHasKey('X_CUSTOM_HEADER', $headers);
    $this->assertEquals('application/json', $headers['CONTENT_TYPE']);
    $this->assertEquals('application/json', $headers['ACCEPT']);
    $this->assertEquals('custom value', $headers['X_CUSTOM_HEADER']);
  }

  public function testGetHeader()
  {
    $server = [
      'HTTP_CONTENT_TYPE' => 'application/json',
      'HTTP_ACCEPT' => 'application/json',
      'HTTP_X_CUSTOM_HEADER' => 'custom value'
    ];
    $request = new Request($server);
    $this->assertEquals('application/json', $request->getHeader('CONTENT_TYPE'));
    $this->assertEquals('application/json', $request->getHeader('ACCEPT'));
    $this->assertEquals('custom value', $request->getHeader('X_CUSTOM_HEADER'));
    $this->assertEquals('', $request->getHeader('NON_EXISTING_HEADER'));
  }

  public function testGetStatusCode()
  {
    $request = new Request();
    $this->assertEquals(200, $request->getStatusCode());
  }
}