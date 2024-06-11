<?php

namespace App\Services\Authenticate;
use App\Services\Authenticate\IAuthResult;

interface IAuthAdapter {
    public function authenticate(): IAuthResult;
}