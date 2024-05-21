<?php
namespace App\Routing;

use App\Middleware\IMiddleware;
use App\Request\IFullRequest;
use App\Middleware\IResponse;

interface IRouter
{
  public static function get(string $path, string $controllerAction, array $middlewares = []);
  public static function post(string $path, string $controllerAction, array $middlewares = []);
  public static function dispatch(IFullRequest $request): IResponse;
}