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


    public function process(IFullRequest $request, IHandler $handler): IResponse
    {
        $path = $request->getPath();
        $method = $request->getMethod();
        // check if the user is authenticated (in the session)
        $authenticated = $this->authService->hasIdentity();

        // Allow access to login form if not authenticated and requesting the login form
        if (!$authenticated && $path === $this->loginPath && $request->getMethod() === 'GET') {
            return $handler->handle($request);
        }

        // Redirect authenticated users away from login page
        if ($authenticated && in_array($path, $this->allowedPaths)) {
            return new RedirectResponse($this->redirectUrl);
        }

        // If not authenticated and not accessing an allowed path, attempt to authenticate
        if (!$authenticated && !in_array($path, $this->allowedPaths)) {
            $authAdapter = $this->authAdapterFactory->createAuthAdapter($request);
            $result = $this->authService->authenticate($authAdapter);

            if (!$result->isValid()) {
                // Authentication failed, redirect to login
                return new RedirectResponse($this->loginPath);
            }

            // Authentication succeeded, redirect to home
            return new RedirectResponse($this->redirectUrl);
        }

        
        return $this->next ? $this->next->process($request, $handler) : $handler->handle($request);
    }
}