<?php
namespace App\Services\Session;

use App\Services\Session\ISessionService;


class SessionService implements ISessionService
{
  private static $started = false;

  private static function startSession()
  {
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }
  }

  public static function start()
  {
    if (!self::$started) {
      self::$started = true;
      self::startSession();
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

  public static function end()
  {
    session_unset();
    session_destroy();
  }
}
