<?php

require_once 'src/controllers/DefaultController.php';
require_once 'src/controllers/QuestsController.php';
require_once 'src/controllers/ErrorController.php';


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
    $action = explode("/", $url)[0];

    if (!array_key_exists($action, self::$routes)) {
      self::renderErrorPage(404, "path not found");
      return;
    }

    $controller = self::$routes[$action];
    $object = new $controller;
    $action = $action ?: 'index';

    try {
      $object->$action();
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