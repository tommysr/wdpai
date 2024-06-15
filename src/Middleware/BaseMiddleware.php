<?php

namespace App\Middleware;

use App\Middleware\IMiddleware;

abstract class BaseMiddleware implements IMiddleware
{
    protected ? IMiddleware $next = null;

    public function setNext(IMiddleware $middleware): IMiddleware
    {
        $this->next = $middleware;
        return $middleware;
    }

    public function removeNext(): void
    {
        $this->next = null;
    }
}