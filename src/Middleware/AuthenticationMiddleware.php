<?php

namespace App\Middleware;

use App\Request\IRequest;
use App\Middleware\BaseMiddleware;

use App\Services\Authenticate\IAuthResult;
use App\Services\Authenticate\IAuthService;
use App\Services\Authenticate\IAuthAdapterFactory;


class AuthenticationMiddleware extends BaseMiddleware
{
    private IAuthService $authService;
    private IAuthAdapterFactory $authAdapterFactory;

    public function __construct(IAuthService $authService, IAuthAdapterFactory $authAdapterFactory){
        $this->authService = $authService;
        $this->authAdapterFactory = $authAdapterFactory;
    }

    public function process(IRequest $request, IHandler $handler): IResponse
    {
        $authAdapter = $this->authAdapterFactory->createAuthAdapter($request);
        $result = $this->authService->authenticate($authAdapter);

        if (!$this->isResponseValid($result)) {
            return new Response(401, ['Content-Type' => 'application/json'], json_encode(['error' => 'Unauthorized']));
        }

        // maybe some RedirectResponse here


        return $this->next->process($request, $handler);
    }

    private function isResponseValid(IAuthResult $result): bool
    {
        return $result->isValid();
    }
}