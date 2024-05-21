<?php
namespace App\Routing;

use App\Request\IRequest;
use App\Routing\IRoute;
use App\Middleware\IMiddleware;

// declare(strict_types=1);

class Route implements IRoute
{
    private string $method;
    private string $path;
    private string $controller;
    private string $action;
    private ?IMiddleware $middleware;
    private array $paramNames = [];

    private function setMiddlewares(array $middlewares): void
    {   
        $this->middleware = null;

        foreach ($middlewares as $middleware) {
            if (!$middleware instanceof IMiddleware) {
                throw new \Exception('Middleware must implement IMiddleware');
            }

            if ($this->middleware) {
                $middleware->setNext($this->middleware);
            } else {
                $this->middleware = $middleware;
            }
        }
    }

    public function __construct(string $method, string $path, string $controller, string $action, array $middlewares = [])
    {
        $this->setMiddlewares($middlewares);
        $this->method = $method;
        $this->path = $path;
        $this->controller = $controller;
        $this->action = $action;
        $this->paramNames = $this->extractParamNames($path);
    }

    private function extractParamNames(string $path): array
    {
        preg_match_all('/\{(\w+)\}/', $path, $matches);
        return $matches[1];
    }

    public function matches(IRequest $request, &$params = []): bool
    {
        if ($this->method !== $request->getMethod()) {
            return false;
        }

        $regex = preg_replace('/\{(\w+)\}/', '(\w+)', $this->path);

        $regex = str_replace('/', '\/', $regex);
        if (preg_match('/^' . $regex . '$/', $request->getPath(), $matches)) {
            array_shift($matches);
            $params = array_combine($this->paramNames, $matches);
            return true;
        }

        return false;
    }

    public function getMiddleware(): ?IMiddleware
    {
        return $this->middleware;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function getAction(): string
    {
        return $this->action;
    }
}