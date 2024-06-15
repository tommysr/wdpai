<?php

namespace App\Middleware\Authentication;

use App\Middleware\IHandler;
use App\Middleware\IResponse;
use App\Middleware\JsonResponse;
use App\Middleware\RedirectResponse;
use App\Request\IFullRequest;
use App\Middleware\BaseMiddleware;

use App\Services\Authenticate\IAuthService;
use App\Services\Authenticate\IAuthAdapterFactory;


class AuthenticationMiddleware extends BaseMiddleware
{
    private string $loginPath;
    private array $redirectUrls;
    private array $allowedPaths;
    private IAuthService $authService;
    private IAuthAdapterFactory $authAdapterFactory;

    public function __construct(IAuthService $authService, IAuthAdapterFactory $authAdapterFactory, array $redirectUrls = ['normal' => '/showQuests', 'creator' => '/showCreatedQuests', 'admin' => '/showQuestsToApproval'], string $loginPath = '/login', array $allowedPaths = ['/login', '/register'])
    {
        $this->redirectUrls = $redirectUrls;
        $this->authService = $authService;
        $this->authAdapterFactory = $authAdapterFactory;
        $this->loginPath = $loginPath;
        $this->allowedPaths = $allowedPaths;
    }

    private function attemptAuthenticate(IFullRequest $request): IResponse
    {
        $authAdapter = $this->authAdapterFactory->createAuthAdapter($request);

        if (!$authAdapter) {
            return new JsonResponse(['errors' => 'invalid authentication method'], 401);
        }

        $result = $this->authService->authenticate($authAdapter);


        if (!$result->isValid()) {
            return new JsonResponse(['errors' => $result->getMessages()], 401);
        }

        $roleName = $result->getIdentity()->getRole()->getName();
        return new JsonResponse(['redirectUrl' => $this->redirectUrls[$roleName]]);
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
            $roleName = $this->authService->getIdentity()->getRole()->getName();
            return new RedirectResponse($this->redirectUrls[$roleName]);
        }

        // If not authenticated and not accessing an allowed path, attempt to authenticate
        if (!$authenticated && !in_array($path, $this->allowedPaths)) {
            $response = $this->attemptAuthenticate($request);

            if ($response->getStatusCode() === 200) {
                return $response;
            } else {
                return new RedirectResponse($this->loginPath);
            }
        }

        // If not authenticated and attempting to login, attempt to authenticate   
        if (!$authenticated && $path === $this->loginPath && $method === 'POST') {
            return $this->attemptAuthenticate($request);
        }

        return $this->next ? $this->next->process($request, $handler) : $handler->handle($request);
    }
}