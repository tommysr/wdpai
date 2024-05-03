<?php


class AuthInterceptor
{
  public static function isLoggedIn()
  {
    if (isset($_SESSION['user'])) {
      return true;
    }

    return false;
  }

  public static function isAdmin()
  {
    if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 'Admin') {
      return true;
    }

    return false;
  }
}