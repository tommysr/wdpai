<?php

namespace App\Services\Authenticate;

use App\Request\IRequest;
use App\Services\Authenticate\IAuthAdapter;
use App\Services\Authenticate\IAuthAdapterFactory;



class AuthAdapterFactory implements IAuthAdapterFactory
{
    public function createAuthAdapter(IRequest $request): ?IAuthAdapter
    {
        $email = $request->post('email');
        $password = $request->post('password');

        return new DBAuthAdapter($email, $password);
    }
}