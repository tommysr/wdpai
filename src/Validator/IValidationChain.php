<?php

namespace App\Validator;

use App\Validator\IValid;

interface IValidationChain
{
  public function getRules(string $key): array;
  public function addRule(string $key, IValid $rule);
  public function addRules(string $key, array $rules);
  public function validateField(string $key, $value): bool|string;
  public function validateFields(array $fields): array;
}
