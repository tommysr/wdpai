<?php

namespace App\Services\Register;
use App\Services\Register\IRegisterResult;

interface IRegisterService
{
  public function register(array $data): IRegisterResult;
}
