<?php

namespace App\Services\Session;

interface ISessionService
{
  public function start();
  public function set(string $key, $value);
  public function has(string $key);
  public function get(string $key, $default = null);
  public function delete(string $key);
  public function end();
}