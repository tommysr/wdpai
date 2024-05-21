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

    public function __construct(IAuthService $authService, IAuthAdapterFactory $authAdapterFactory, string $loginPath = '/login', array $allowedPaths = ['/login', '/register'], string $redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
        $this->authService = $authService;
        $this->authAdapterFactory = $authAdapterFactory;
        $this->loginPath = $loginPath;
        $this->allowedPaths = $allowedPaths;
    }

    public function process(IFullRequest $request, IHandler $handler): IResponse
    {
        $authAdapter = $this->authAdapterFactory->createAuthAdapter($request);
        $result = $this->authService->authenticate($authAdapter);

        $path = $request->getPath();
        $authenticated = $result->isValid();

        if ($authenticated && in_array($path, $this->allowedPaths)) {
            return new RedirectResponse($this->redirectUrl);
        }

        if (!$authenticated && !in_array($path, $this->allowedPaths)) {
            return new RedirectResponse($this->loginPath);
        }

        return $this->next->process($request, $handler);
    }
}