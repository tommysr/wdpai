<?php

class Router
{
  public static $routes;

  public static function get(string $url, Middleware $middleware, $view)
  {
    self::$routes[$url] = ['middleware' => $middleware, 'view' => $view];
  }

  public static function post(string $url, Middleware $middleware, $view)
  {
    self::$routes[$url] = ['middleware' => $middleware, 'view' => $view];
  }

  public static function run($url)
  {
    $urlParts = explode("/", $url);
    $routeName = array_shift($urlParts);
    $params = $urlParts;

    if (!array_key_exists($routeName, self::$routes)) {
      self::renderErrorPage(404, "Path not found");
      return;
    }

    $route = self::$routes[$routeName];
    $middleware = $route['middleware'];
    $controllerAction = $route['action'];

    // Execute middleware
    if ($middleware !== null) {
      $middleware->handle();
    }

    list($controllerName, $actionName) = explode('@', $controllerAction);
    $controller = new $controllerName();
    $action = empty($actionName) ? 'index' : $actionName;

    try {
      $controller->$action(isset($params[0]) ? $params[0] : '');
    } catch (Exception $e) {
      error_log('Error occurred: ' . $e->getMessage());
      self::renderErrorPage(500, 'Internal server error, try again later');
    }
  }

  private static function renderErrorPage($errorCode, $message = '')
  {
    $controller = new ErrorController();

    switch ($errorCode) {
      case 404:
        $controller->notFound($message);
        break;
      case 500:
        $controller->serverError($message);
        break;
      default:
        $controller->serverError($message);
    }
  }
}
