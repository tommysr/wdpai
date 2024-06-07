<?php

namespace App\Controllers\Interfaces;

use App\Middleware\IResponse;
use App\Request\IRequest;

interface IProfileController extends IRootController
{
    public function getShowProfile(IRequest $request): IResponse;
    public function postChangePassword(IRequest $request): IResponse;
}