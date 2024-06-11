<?php
namespace App\Routing;

use App\Middleware\IMiddleware;
use App\Request\IRequest;

interface IRoute
{
    public function matches(IRequest $request,  &$params = []): bool;
    public function getMiddlewares(): array;
    public function getController(): string;
    public function getAction(): string;
    public function getMethod(): string;   
    // public function buildMiddlewares(): void;
}
