<?php

class SessionService
{
  public function __construct()
  {
    $this->start();
  }

  public static function start()
  {
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }
  }

  public static function set($key, $value)
  {
    $_SESSION[$key] = $value;
  }

  public static function has($key)
  {
    return isset($_SESSION[$key]);
  }

  public static function get($key)
  {
    return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
  }

  public static function delete($key)
  {
    unset($_SESSION[$key]);
  }

  public function end()
  {
    session_unset();
    session_destroy();
  }
}
