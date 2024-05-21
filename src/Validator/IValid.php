<?php
namespace App\Validator;

interface IValid
{
  public function validate($value): bool | string;
}