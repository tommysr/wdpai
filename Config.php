<?php
require_once "Singleton.php";


interface IConfig
{
  public function getValue(string $key): string;
  public function setValue(string $key, string $value): void;
}

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
