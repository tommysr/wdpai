<?php

namespace App\Controllers;

interface ILoginController
{
  public function login(): void;
  public function logout(): void;
}
