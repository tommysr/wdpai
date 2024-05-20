<?php

namespace App\Services\Authenticate;

use App\Request\IRequest;

interface IAuthAdapterFactory
{
    public function createAuthAdapter(IRequest $request): ?IAuthAdapter;
}