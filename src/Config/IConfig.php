<?php
namespace App\Config;

interface IConfig
{
  public function getValue(string $key): string;
  public function setValue(string $key, string $value): void;
}
