<?php

namespace App\Services\Register;
use App\Result\IResult;


interface IRegisterStrategy
{
  public function register(): IResult;
}