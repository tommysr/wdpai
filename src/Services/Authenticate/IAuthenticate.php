<?php

namespace App\Services\Authenticate;

use App\Models\User;

interface IAuthenticate
{
  public function login(string $email, string $password): User;
}
