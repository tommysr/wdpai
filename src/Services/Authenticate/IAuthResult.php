<?php

namespace App\Services\Authenticate;
use App\Services\Authenticate\IIdentity;

interface IAuthResult {
    public function getIdentity(): IIdentity;
    public function getMessages(): array;   
    public function isValid(): bool;
}