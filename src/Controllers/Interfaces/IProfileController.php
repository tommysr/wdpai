<?php

namespace App\Controllers\Interfaces;

use App\Middleware\IResponse;
use App\Request\IFullRequest;

interface IProfileController extends IRootController
{
    public function getShowProfile(IFullRequest $request): IResponse;
    public function postChangePassword(IFullRequest $request): IResponse;
}