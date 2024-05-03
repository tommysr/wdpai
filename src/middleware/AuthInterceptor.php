<?php


class AuthInterceptor
{
  public static function check()
  {
    if (isset($_SESSION['userId'])) {
      return true;
    }

    return false;
  }

  public static function checkAdmin()
  {
    if (isset($_SESSION['userId']) && $_SESSION['userRole'] == 'Admin') {
      return true;
    }

    return false;
  }
}