<?php

namespace App\Services\Register;

interface IRegisterService
{
  public function register(): IRegisterResult;
}