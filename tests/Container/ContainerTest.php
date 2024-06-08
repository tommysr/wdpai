<?php
use PHPUnit\Framework\TestCase;
use App\Container\Container;

interface IRequest
{
  public function getCode();
}

class Request implements IRequest
{
  public function getCode()
  {
    return 100;
  }
}

class HomeController
{
  private IRequest $request;

  public function __construct(IRequest $request)
  {
    $this->request = $request;
  }

  public function index(IRequest $request, $param1 = 'default1', $param2 = 'default2')
  {
    return [$request, $param1, $param2];
  }
}

interface ISomeService
{
  public function b();
}

class SomeService implements ISomeService
{
  private IOtherService $otherService;

  public function __construct(IOtherService $otherService)
  {
    $this->otherService = $otherService;
  }

  public function b()
  {
    return $this->otherService->a();
  }
}

interface IOtherService
{
  public function a();
}

class OtherService implements IOtherService
{
  public function a()
  {
    return 'a';
  }
}

class ContainerTest extends TestCase
{
  public function testSetAndGetBinding()
  {
    $container = new Container();
    $container->set(IRequest::class, function ($c) {
      return new Request();
    });
    $container->set('OtherService', function ($c) {
      return new OtherService();
    });

    $container->set('SomeService', function ($c) {
      return new SomeService($c->get('OtherService'));
    });

    $someService = $container->get('SomeService');

    $this->assertInstanceOf(SomeService::class, $someService);
  }

  public function testSingletonBinding()
  {
    $container = new Container();
    $container->set(IRequest::class, function ($c) {
      return new Request();
    });
    $container->singleton('OtherService', function ($c) {
      return new OtherService();
    });

    $instance1 = $container->get('OtherService');
    $instance2 = $container->get('OtherService');

    $this->assertSame($instance1, $instance2);
  }

  public function testBuildWithDependencies()
  {
    $container = new Container();
    $container->set(IRequest::class, function ($c) {
      return new Request();
    });
    $container->set(IOtherService::class, function ($c) {
      return new OtherService();
    });

    $container->set(ISomeService::class, function ($c) {
      return new SomeService($c->get(IOtherService::class));
    });

    $someService = $container->build(SomeService::class);

    $this->assertInstanceOf(ISomeService::class, $someService);
  }

  public function testCallMethodWithRequest()
  {
    $container = new Container();
    $container->set(IRequest::class, function ($c) {
      return new Request();
    });
    $controller = new HomeController($container->get(IRequest::class));
    $result = $container->callMethod($controller, 'index');

    $this->assertInstanceOf(IRequest::class, $result[0]);
    $this->assertEquals('default1', $result[1]);
    $this->assertEquals('default2', $result[2]);
  }

  public function testCallMethodWithProvidedParameters()
  {
    $container = new Container();
    $container->set(IRequest::class, function ($c) {
      return new Request();
    });
    $controller = new HomeController($container->get(IRequest::class));

    $result = $container->callMethod($controller, 'index', ['param1' => 'value1', 'param2' => 'value2']);

    $this->assertInstanceOf(Request::class, $result[0]);
    $this->assertEquals('value1', $result[1]);
    $this->assertEquals('value2', $result[2]);
  }

  public function testCallMethodWithPartialProvidedParameters()
  {
    $container = new Container();
    $container->set(IRequest::class, function ($c) {
      return new Request();
    });
    $controller = new HomeController($container->get(IRequest::class));

    $result = $container->callMethod($controller, 'index', ['param1' => 'value1']);

    $this->assertInstanceOf(Request::class, $result[0]);
    $this->assertEquals('value1', $result[1]);
    $this->assertEquals('default2', $result[2]);
  }

  public function testBuildThrowsExceptionForUninstantiableClass()
  {
    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Class "UninstantiableClass" does not exist.');

    $container = new Container();
    $container->set(IRequest::class, function ($c) {
      return new Request();
    });
    $container->build('UninstantiableClass');
  }

  public function testGetThrowsExceptionForUnboundIdentifier()
  {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('No binding found for [UnboundService].');

    $container = new Container();
    $container->set(IRequest::class, function ($c) {
      return new Request();
    });
    $container->get('UnboundService');
  }

  public function testCallMethodThrowsExceptionForUnresolvableDependency()
  {
    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('Unresolvable dependency [param3] in method [index]');

    $container = new Container();
    $container->set(IRequest::class, function ($c) {
      return new Request();
    });

    $controller = new class {
      public function index(IRequest $request, $param3)
      {
      }
    };

    $container->callMethod($controller, 'index');
  }
}
