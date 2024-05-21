<?php

namespace App\Services\Authenticate;

use App\Services\Authenticate\IAuthResult;
use App\Services\Authenticate\IAuthAdapter;

interface IAuthService {
    public function authenticate(IAuthAdapter $adapter): IAuthResult;
    public function hasIdentity(): bool;
    public function getIdentity(): string;
    public function clearIdentity();
}