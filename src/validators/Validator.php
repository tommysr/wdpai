<?php

class Validator
{
  public static function validateEmail($email)
  {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return false;
    }
    return true;
  }

  public static function validatePassword($password)
  {
    if (strlen($password) < 8) {
      return false;
    }
    return true;
  }

  public static function validateUsername($username)
  {
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
      return false;
    }
    return true;
  }

  public static function validateConfirmedPassword($password, $confirmedPassword)
  {
    if ($password !== $confirmedPassword) {
      return false;
    }
    return true;
  }
}
