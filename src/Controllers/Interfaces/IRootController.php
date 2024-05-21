<?php

namespace App\Controllers\Interfaces;

use App\Request\IRequest;
use App\Middleware\IResponse;

interface IRootController
{
    public function getIndex(IRequest $request): IResponse;
}