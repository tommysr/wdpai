<?php

namespace App\Services\Authenticate;

use App\Result\IResult;
use App\Services\Authenticate\IIdentity;

interface IAuthResult extends IResult
{
    public function getIdentity(): ?IIdentity;
}