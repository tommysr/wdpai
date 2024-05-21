<?php
namespace App\Middleware;

use App\Request\IRequest;
use App\Middleware\IResponse;

interface IHandler
{
    public function handle(IRequest $request): IResponse;
}