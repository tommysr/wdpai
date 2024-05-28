<?php

namespace App\Services\Session;

interface ISessionService
{
  public static function start();
  public static function set(string $key, $value);
  public static function has(string $key);
  public static function get(string $key, $default = null);
  public static function delete(string $key);
  public static function end();
}