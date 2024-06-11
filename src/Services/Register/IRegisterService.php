<?php

namespace App\Services\Register;
use App\Result\IResult;

interface IRegisterService
{
  public function register(): IResult;
}