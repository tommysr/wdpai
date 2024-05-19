<?php

namespace App\Controllers;

use App\Request\IRequest;

interface IRootController
{
    public function index(IRequest $request);
}