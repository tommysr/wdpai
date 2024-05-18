<?php
namespace App\Routing;

use App\Middleware\IMiddleware;
use App\Request\IRequest;

interface IRouter
{
  public static function get(string $path, string $controllerAction, ?IMiddleware $middleware);
  public static function post(string $path, string $controllerAction, ?IMiddleware $middleware);
  public static function dispatch(IRequest $request);
}