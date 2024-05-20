<?php

namespace App\Middleware;

use App\Middleware\IMiddleware;

abstract class BaseMiddleware implements IMiddleware
{
    protected $next;

    public function setNext(IMiddleware $middleware): IMiddleware
    {
        $this->next = $middleware;
        return $middleware;
    }
}