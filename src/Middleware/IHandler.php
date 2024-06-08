<?php
namespace App\Middleware;

use App\Request\IFullRequest;
use App\Middleware\IResponse;

interface IHandler
{
    public function handle(IFullRequest $request): IResponse;
}