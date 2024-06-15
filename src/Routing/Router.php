<?php
namespace App\Routing;

// declare(strict_types=1);

use App\Container\IContainer;
use App\Middleware\IHandler;
use App\Middleware\IMiddleware;
use App\Middleware\RedirectResponse;
use Exception;
use App\Request\IFullRequest;
use App\Request\IRequest;
use App\Routing\IRouter;
use App\Controllers\Interfaces\IRootController;
use App\Middleware\IResponse;
use App\Middleware\BaseResponse;


class Router implements IRouter
{
  private array $routes = [];
  private IContainer $container;

  public function __construct(IContainer $container)
  {
    $this->container = $container;
  }

  public function get(string $path, string $controllerAction, array $middlewares = [])
  {
    $this->addRoute('GET', $path, $controllerAction, $middlewares);
  }

  public function post(string $path, string $controllerAction, array $middlewares = [])
  {
    $this->addRoute('POST', $path, $controllerAction, $middlewares);
  }

  private function addRoute(string $method, string $path, string $controllerAction, array $middlewares = [])
  {
    list($controller, $action) = explode('@', $controllerAction);
    $this->routes[] = new Route($method, $path, $controller, $action, $middlewares);
  }

  private function buildMiddleware(array $middlewares): ?IMiddleware
  {
    if (empty($middlewares)) {
      return null;
    }

    $firstMiddleware = null;
    $lastMiddleware = null;

    foreach ($middlewares as $middleware) {
      $middlewareInstance = $this->container->get($middleware);

      if (!$middlewareInstance instanceof IMiddleware) {
        throw new MissingImplementationException("Middleware must implement IMiddleware");
      }

      if ($lastMiddleware) {
        $lastMiddleware->setNext($middlewareInstance);
      }

      if (!$firstMiddleware) {
        $firstMiddleware = $middlewareInstance;
      }

      $lastMiddleware = $middlewareInstance;
    }

    $lastMiddleware->removeNext();

    return $firstMiddleware;
  }

  public function dispatch(IFullRequest $request): IResponse
  {
    foreach ($this->routes as $route) {
      $params = [];

      if ($route->matches($request, $params)) {

        $controllerName = $route->getController();
        $actionName = $route->getAction();
        $middlewaresClasses = $route->getMiddlewares();
        $middleware = $this->buildMiddleware($middlewaresClasses);

        $controllerClassName = "App\\Controllers\\" . $controllerName;
        $action = empty($actionName) ? 'index' : $actionName;
        $request = $request->withAttribute('controller', $controllerName)->withAttribute('action', $action)->withAttribute('params', $params);

        $this->container->set(IFullRequest::class, function () use ($request) {
          return $request;
        });

        $controllerInstance = $this->container->build($controllerClassName);

        if (!$controllerInstance instanceof IRootController) {
          throw new MissingImplementationException('Controller must implement RouteInterface');
        }

        if (!$controllerInstance instanceof IHandler) {
          throw new MissingImplementationException('Controller must implement IHandler');
        }

        $this->container->set(IHandler::class, function () use ($controllerInstance) {
          return $controllerInstance;
        });

        if ($middleware) {
          return $this->container->callMethod($middleware, 'process', [$request, $controllerInstance]);
        }


        return $this->container->callMethod($controllerInstance, 'handle', [$request]);
      }
    }

    return new RedirectResponse('/error/404', ['route not found']);
  }
}

class MissingImplementationException extends Exception
{
}
