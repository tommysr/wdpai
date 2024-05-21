<?php

namespace App\Controllers\Interfaces;

use App\Request\IRequest;
use App\Middleware\IResponse;

interface IRootController
{
    public function index(IRequest $request): IResponse;
}