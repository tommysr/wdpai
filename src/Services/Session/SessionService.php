<?php
namespace App\Services\Session;

use App\Services\Session\ISessionService;

class SessionService implements ISessionService
{
  private $started = false;

  private function startSession()
  {
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }
  }

  public function start()
  {
    if (!$this->started) {
      $this->started = true;
      $this->startSession();
    }
  }

  public function set($key, $value)
  {
    $_SESSION[$key] = $value;
  }

  public function has($key)
  {
    return isset($_SESSION[$key]);
  }

  public function get($key, $default = null)
  {
    return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
  }

  public function delete($key)
  {
    unset($_SESSION[$key]);
  }

  public function end()
  {
    if ($this->started) {
      session_unset();
      session_destroy();
      $this->started = false;
    }
  }
}
