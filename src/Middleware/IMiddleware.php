<?php

namespace App\Middleware;

interface IMiddleware
{
  /**
   * Handle the middleware logic.
   *
   * @return void
   */
  public function handle(): void;

  /**
   * Set the next middleware in the chain.
   *
   * @param IMiddleware $middleware The next middleware in the chain.
   * @return IMiddleware Returns the next middleware.
   */
  public function setNext(IMiddleware $middleware): IMiddleware;
}
