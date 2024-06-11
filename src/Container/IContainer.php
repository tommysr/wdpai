<?php

namespace App\Container;

interface IContainer
{
  public function set($id, $factory);
  public function singleton($id, $factory);
  public function get($id);
  public function build(string $class);
  public function callMethod($class, string $method, array $params = []);
}