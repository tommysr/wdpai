<?php

namespace App\Services\Authenticate;

use App\Services\Authenticate\IAuthResult;
use App\Services\Authenticate\IAuthAdapter;
use App\Services\Authenticate\IIdentity;

interface IAuthService {
    public function authenticate(IAuthAdapter $adapter): IAuthResult;
    public function saveIdentity(IIdentity $identity);
    public function hasIdentity(): bool;
    public function getIdentity(): IIdentity;
    public function clearIdentity();
}