<?php

namespace App\Middleware;

use App\Request\IFullRequest;
use App\Middleware\BaseMiddleware;

use App\Services\Authenticate\IAuthService;
use App\Services\Authenticate\IAuthAdapterFactory;


class AuthenticationMiddleware extends BaseMiddleware
{
    private string $loginPath;
    private string $redirectUrl;
    private array $allowedPaths;
    private IAuthService $authService;
    private IAuthAdapterFactory $authAdapterFactory;

    public function __construct(IAuthService $authService, IAuthAdapterFactory $authAdapterFactory, string $redirectUrl = '/', string $loginPath = '/login', array $allowedPaths = ['/login', '/register'])
    {
        $this->redirectUrl = $redirectUrl;
        $this->authService = $authService;
        $this->authAdapterFactory = $authAdapterFactory;
        $this->loginPath = $loginPath;
        $this->allowedPaths = $allowedPaths;
    }

    private function attemptAuthenticate(IFullRequest $request): IResponse
    {
        $authAdapter = $this->authAdapterFactory->createAuthAdapter($request);

        if (!$authAdapter) {
            return new RedirectResponse($this->loginPath);
        }

        $result = $this->authService->authenticate($authAdapter);

        if (!$result->isValid()) {
            return new RedirectResponse($this->loginPath);
        }

        return new RedirectResponse($this->redirectUrl);
    }

    public function process(IFullRequest $request, IHandler $handler): IResponse
    {
        $path = $request->getPath();
        $method = $request->getMethod();
        // check if the user is authenticated (in the session)
        $authenticated = $this->authService->hasIdentity();


        // Allow access to login form if not authenticated and requesting the login form
        if (!$authenticated && $path === $this->loginPath && $method === 'GET') {
            return $handler->handle($request);
        }

        // Redirect authenticated users away from login page and register page if authenticated
        if ($authenticated && in_array($path, $this->allowedPaths)) {
            return new RedirectResponse($this->redirectUrl);
        }

        // If not authenticated and not accessing an allowed path, attempt to authenticate
        if (!$authenticated && !in_array($path, $this->allowedPaths)) {
            return $this->attemptAuthenticate($request);
        }

        // If not authenticated and attempting to login, attempt to authenticate   
        if (!$authenticated && $path === $this->loginPath && $method === 'POST') {
            return $this->attemptAuthenticate($request);
        }

        return $this->next ? $this->next->process($request, $handler) : $handler->handle($request);
    }
}