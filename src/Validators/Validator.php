<?php

require_once __DIR__ . '/../exceptions/User.php';

interface IValid
{
  public function validate($value): bool;
}

class EmailValidationRule implements IValid
{
  public function validate($value): bool
  {
    return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
  }
}

class PasswordLengthValidationRule implements IValid
{
  public function validate($value): bool
  {
    return strlen($value) >= 8;
  }
}

class UsernameFormatValidationRule implements IValid
{
  public function validate($value): bool
  {
    return preg_match('/^[a-zA-Z0-9_]+$/', $value) === 1;
  }
}

class ConfirmedPasswordValidationRule implements IValid
{
  private $password;

  public function __construct($password)
  {
    $this->password = $password;
  }

  public function validate($confirmedPassword): bool
  {
    return $this->password === $confirmedPassword;
  }
}

interface IValidationChain
{
  public function getRules(string $key): array;
  public function addRule(string $key, IValid $rule);
  public function validateField(string $key, $value): bool;
  public function validateFields(array $fields): bool;
}

class Validator implements IValidationChain
{
  private $rules = [];

  public function getRules(string $key): array
  {
    return $this->rules[$key];
  }

  public function addRule(string $key, IValid $rule)
  {
    $this->rules[$key][] = $rule;
  }

  public function validateField(string $key, $value): bool
  {
    if (!isset($this->rules[$key])) {
      throw new NoValidationRules("No validation rules defined for field $key.");
    }

    foreach ($this->rules[$key] as $rule) {
      if (!$rule->validate($value)) {
        return false;
      }
    }
    return true;
  }

  public function validateFields(array $fields): bool
  {
    foreach ($fields as $key => $value) {
      if (!$this->validateField($key, $value)) {
        return false;
      }
    }
    return true;
  }
}
