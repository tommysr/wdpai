<?php

namespace App\Validator;

use App\Validator\IValidationChain;
use App\Validator\IValid;
use Exception;


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
      throw new Exception("No validation rules defined for field $key.");
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
