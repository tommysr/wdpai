<?php

class ValidationException extends Exception
{
  public function errorMessage()
  {
    return "validation failed";
  }
}

class AlreadyRegistered extends Exception
{
  public function errorMessage()
  {
    return "user already registered";
  }
}

class UsernameTaken extends Exception
{
  public function errorMessage()
  {
    return "username already taken";
  }
}

class NoValidationRules extends Exception
{
  public function errorMessage()
  {
    return "no validation rules";
  }
}