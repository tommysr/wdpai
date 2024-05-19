<?php
namespace App\Config;

use App\Config\IConfig;

class Config implements IConfig
{
  private $hashmap = [];

  protected function __construct()
  {
  }

  public function getValue(string $key): string
  {
    return $this->hashmap[$key];
  }


  public function setValue(string $key, string $value): void
  {
    $this->hashmap[$key] = $value;
  }
}
