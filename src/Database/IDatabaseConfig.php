<?php
namespace App\Database;

interface IDatabaseConfig
{
  public function getUsername(): string;
  public function getPassword(): string;
  public function getHost(): string;
  public function getDatabase(): string;
  public function getPort(): string;
}
