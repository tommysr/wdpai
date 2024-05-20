<?php

namespace App\Middleware;
use App\Request\IRequest;
use App\Middleware\IResponse;

interface IMiddleware
{
  /**
   * Handle the middleware logic.
   * @param IRequest $request The request object.
   * @param IHandler $handler The handler object.
   * @return IResponse The response object.
   */
  public function process(IRequest $request, IHandler $handler): IResponse;

  /**
   * Set the next middleware in the chain.
   *
   * @param IMiddleware $middleware The next middleware in the chain.
   * @return IMiddleware Returns the next middleware.
   */
  public function setNext(IMiddleware $middleware): IMiddleware;
}
