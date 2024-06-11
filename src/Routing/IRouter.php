<?php
namespace App\Routing;

use App\Middleware\IMiddleware;
use App\Request\IFullRequest;
use App\Middleware\IResponse;

interface IRouter
{
  public function get(string $path, string $controllerAction, array $middlewares = []);
  public function post(string $path, string $controllerAction, array $middlewares = []);
  public function dispatch(IFullRequest $request): IResponse;
}