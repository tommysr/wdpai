<?php

namespace App\Services\Register;


interface IRegisterStrategy
{
  public function register(): IRegisterResult;
}