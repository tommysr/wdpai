<?php
namespace App\Routing;

// declare(strict_types=1);

use App\Middleware\IHandler;
use Exception;
use App\Request\IFullRequest;
use App\Middleware\IMiddleware;
use App\Routing\IRouter;
use App\Controllers\IRootController;
use App\Middleware\IResponse;


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
        $middleware = $route->getMiddleware();

        $controllerClassName = "App\\Controllers\\" . $controllerName;
        $action = empty($actionName) ? 'index' : $actionName;
        $request = $request->withAttribute('action', $action);
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

        return call_user_func_array([$controllerInstance, $action], array_merge([$request], $params));
      }
    }


    // maybe return some response with raw code, but how do i handle it later
    throw new Exception('Route not found');
  }
}

// class Router
// {
//   public static $routes;

//   public static function get(string $url, Middleware $middleware, $view)
//   {
//     self::$routes[$url] = ['middleware' => $middleware, 'view' => $view];
//   }

//   public static function post(string $url, Middleware $middleware, $view)
//   {
//     self::$routes[$url] = ['middleware' => $middleware, 'view' => $view];
//   }

//   public static function run($url)
//   {
//     $urlParts = explode("/", $url);
//     $routeName = array_shift($urlParts);
//     $params = $urlParts;

//     if (!array_key_exists($routeName, self::$routes)) {
//       self::renderErrorPage(404, "Path not found");
//       return;
//     }

//     $route = self::$routes[$routeName];
//     $middleware = $route['middleware'];
//     $controllerAction = $route['action'];

//     // Execute middleware
//     if ($middleware !== null) {
//       $middleware->handle();
//     }

//     list($controllerName, $actionName) = explode('@', $controllerAction);
//     $controller = new $controllerName();
//     $action = empty($actionName) ? 'index' : $actionName;

//     try {
//       $controller->$action(isset($params[0]) ? $params[0] : '');
//     } catch (Exception $e) {
//       error_log('Error occurred: ' . $e->getMessage());
//       self::renderErrorPage(500, 'Internal server error, try again later');
//     }
//   }

//   private static function renderErrorPage($errorCode, $message = '')
//   {
//     $controller = new ErrorController();

//     switch ($errorCode) {
//       case 404:
//         $controller->notFound($message);
//         break;
//       case 500:
//         $controller->serverError($message);
//         break;
//       default:
//         $controller->serverError($message);
//     }
//   }
// }