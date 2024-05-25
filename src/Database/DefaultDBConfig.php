<?php
namespace App\Database;
use App\Config\Config;
use App\Database\IDatabaseConfig;

class DefaultDBConfig extends Config implements IDatabaseConfig
{
  const USERNAME_KEY = 'username';
  const PASSWORD_KEY = 'password';
  const HOST_KEY = 'host';
  const DATABASE_KEY = 'database';
  const PORT_KEY = 'port';

  public function __construct()
  {
    parent::__construct();
    $this->initialize();
  }

  private function initialize(): void
  {
    $this->setValue(self::USERNAME_KEY, 'docker');
    $this->setValue(self::PASSWORD_KEY, 'docker');
    $this->setValue(self::HOST_KEY, 'db');
    $this->setValue(self::DATABASE_KEY, 'db2');
    $this->setValue(self::PORT_KEY, '5432');
  }

  public function getUsername(): string
  {
    return $this->getValue(self::USERNAME_KEY);
  }

  public function getPassword(): string
  {
    return $this->getValue(self::PASSWORD_KEY);
  }

  public function getHost(): string
  {
    return $this->getValue(self::HOST_KEY);
  }

  public function getDatabase(): string
  {
    return $this->getValue(self::DATABASE_KEY);
  }

  public function getPort(): string
  {
    return $this->getValue(self::PORT_KEY);
  }
}
