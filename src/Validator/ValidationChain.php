<?php

namespace App\Validator;

use App\Validator\IValidationChain;
use App\Validator\IValid;


class ValidationChain implements IValidationChain
{
  private array $rules = [];

  public function getRules(string $key): array
  {
    return $this->rules[$key] ?? [];
  }

  public function addRule(string $key, IValid $rule)
  {
    $this->rules[$key][] = $rule;
  }

  public function addRules(string $key, array $rules)
  {
    foreach ($rules as $rule) {
      $this->addRule($key, $rule);
    }
  }

  public function validateField(string $key, $value): bool|string
  {
    if (!isset($this->rules[$key])) {
      throw new ValidationRuleNotDefined("No validation rules defined for field $key.");
    }

    $rules = $this->getRules($key);
    foreach ($rules as $rule) {
      $res = $rule->validate($value);

      if ($res !== true) {
        return $res;
      }
    }

    return true;
  }

  public function validateFields(array $fields): array
  {
    $errors = [];
    foreach ($fields as $key => $value) {
      $fieldErrors = $this->validateField($key, $value);
      if ($fieldErrors !== true) {
        $errors[$key] = $fieldErrors;
      }
    }
    return $errors;
  }
}

class ValidationRuleNotDefined extends \Exception
{
}