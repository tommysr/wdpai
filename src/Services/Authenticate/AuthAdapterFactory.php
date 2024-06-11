<?php

namespace App\Services\Authenticate;

use App\Repository\IUserRepository;
use App\Request\IFullRequest;
use App\Services\Authenticate\IAuthAdapter;
use App\Services\Authenticate\IAuthAdapterFactory;


class AuthAdapterFactory implements IAuthAdapterFactory
{
    private IUserRepository $userRepository;
    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createAuthAdapter(IFullRequest $request): ?IAuthAdapter
    {
        $bodyParams = $request->getParsedBody();

        if (!isset($bodyParams['email']) || !isset($bodyParams['password'])) {
            return null;
        }

        $email = $request->getParsedBodyParam('email');
        $password = $request->getParsedBodyParam('password');
        return new DBAuthAdapter($email, $password, $this->userRepository);
    }
}