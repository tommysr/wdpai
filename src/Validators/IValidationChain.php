<?php

namespace App\Validator;

use App\Validator\IValid;

interface IValidationChain
{
  public function getRules(string $key): array;
  public function addRule(string $key, IValid $rule);
  public function validateField(string $key, $value): bool;
  public function validateFields(array $fields): bool;
}
