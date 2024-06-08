<?php

namespace App\Controllers\Interfaces;

use App\Request\IFullRequest;
use App\Middleware\IResponse;

interface IRootController
{
    public function getIndex(IFullRequest $request): IResponse;
}