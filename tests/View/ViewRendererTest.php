<?php

use PHPUnit\Framework\TestCase;
use App\View\ViewRenderer;



class ViewRendererTest extends TestCase
{
  public function testRender()
  {
    $viewPath = '/app/tests/View/';
    $renderer = new ViewRenderer($viewPath);

    $template = 'index';
    $variables = ['title' => 'Welcome', 'world' => "World"];
    $content = 'hello';

    $expectedOutput = '<html><head><title>Welcome</title></head><body>Hello, World!</body></html>';
    $this->assertEquals($expectedOutput, $renderer->render($template, $variables, $content));
  }

  public function testRenderThrowsExceptionForMissingTemplate()
  {
    $viewPath = '/path/to/views';
    $renderer = new ViewRenderer($viewPath);

    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Template file not found');

    $template = 'nonexistent';
    $renderer->render($template);
  }
}