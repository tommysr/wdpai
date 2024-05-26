<?php
namespace App\Routing;

// declare(strict_types=1);

use App\Middleware\IHandler;
use Exception;
use App\Request\IFullRequest;
use App\Routing\IRouter;
use App\Controllers\Interfaces\IRootController;
use App\Middleware\IResponse;
use App\Middleware\BaseResponse;

class Router implements IRouter
{
  private static array $routes = [];

  public static function get(string $path, string $controllerAction, array $middlewares = [])
  {
    self::addRoute('GET', $path, $controllerAction, $middlewares);
  }

  public static function post(string $path, string $controllerAction, array $middlewares = [])
  {
    self::addRoute('POST', $path, $controllerAction, $middlewares);
  }

  private static function addRoute(string $method, string $path, string $controllerAction, array $middlewares = [])
  {
    list($controller, $action) = explode('@', $controllerAction);
    self::$routes[] = new Route($method, $path, $controller, $action, $middlewares);
  }

  public static function dispatch(IFullRequest $request): IResponse
  {
    foreach (self::$routes as $route) {
      $params = [];

      if ($route->matches($request, $params)) {
        $controllerName = $route->getController();
        $actionName = $route->getAction();
        $route->buildMiddlewares();
        $middleware = $route->getMiddleware();

        $controllerClassName = "App\\Controllers\\" . $controllerName;
        $action = empty($actionName) ? 'index' : $actionName;
        $request = $request->withAttribute('controller', $controllerName)->withAttribute('action', $action)->withAttribute('params', $params);
        $controllerInstance = new $controllerClassName($request);

        if (!$controllerInstance instanceof IRootController) {
          throw new Exception('Controller must implement RouteInterface');
        }

        if (!$controllerInstance instanceof IHandler) {
          throw new Exception('Controller must implement IHandler');
        }

        if ($middleware) {
          return $middleware->process($request, $controllerInstance);
        }

        return $controllerInstance->handle($request);
      }
    }

    return new BaseResponse(404, [], 'Route not found');
  }
}