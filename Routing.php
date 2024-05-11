<?php

require_once 'src/controllers/QuestsController.php';
require_once 'src/controllers/ErrorController.php';
require_once 'src/controllers/AuthController.php';
require_once 'src/controllers/GameController.php';


class Router
{
  public static $routes;

  public static function get($url, $view)
  {
    self::$routes[$url] = $view;
  }

  public static function post($url, $view)
  {
    self::$routes[$url] = $view;
  }

  public static function run($url)
  {
    $urlParts = explode("/", $url);
    $action = array_shift($urlParts);
    $params = $urlParts;


    if (!array_key_exists($action, self::$routes)) {
      self::renderErrorPage(404, "path not found");
      return;
    }

    $controller = self::$routes[$action];
    $object = new $controller;
    $action = empty($action) ? 'index' : $action;

    try {
      $object->$action(isset($params[0]) ? $params[0] : '');
    } catch (Exception $e) {
      error_log('Error occurred: ' . $e->getMessage());

      self::renderErrorPage(500, 'internal server error, try again later');
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