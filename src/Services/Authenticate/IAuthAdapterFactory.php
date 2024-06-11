<?php

namespace App\Services\Authenticate;

use App\Request\IFullRequest;

interface IAuthAdapterFactory
{
    public function createAuthAdapter(IFullRequest $request): ?IAuthAdapter;
}