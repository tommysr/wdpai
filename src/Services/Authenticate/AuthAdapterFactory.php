<?php

namespace App\Services\Authenticate;

use App\Request\IFullRequest;
use App\Services\Authenticate\IAuthAdapter;
use App\Services\Authenticate\IAuthAdapterFactory;


class AuthAdapterFactory implements IAuthAdapterFactory
{
    public function createAuthAdapter(IFullRequest $request): ?IAuthAdapter
    {
        $bodyParams = $request->getParsedBody();

        // could also throw error
        if (!isset($bodyParams['email']) || !isset($bodyParams['password'])) {
            return null;
        }

        $email = $request->getParsedBodyParam('email');
        $password = $request->getParsedBodyParam('password');
        return new DBAuthAdapter($email, $password);
    }
}