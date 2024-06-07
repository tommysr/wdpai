<?php

use PHPUnit\Framework\TestCase;
use App\Database\DefaultDBConfig;


class ConfigTest extends TestCase
{

  public function testSetValue()
  {
    $config = new DefaultDBConfig();
    $config->setValue('key', 'value');
    $result = $config->getValue('key');
    $this->assertEquals('value', $result);
  }


  public function testGetUsername()
  {
    $config = new DefaultDBConfig();
    $username = $config->getUsername();
    $this->assertEquals('docker', $username);
  }

  public function testGetPassword()
  {
    $config = new DefaultDBConfig();
    $password = $config->getPassword();
    $this->assertEquals('docker', $password);
  }

  public function testGetHost()
  {
    $config = new DefaultDBConfig();
    $host = $config->getHost();
    $this->assertEquals('db', $host);
  }

  public function testGetDatabase()
  {
    $config = new DefaultDBConfig();
    $database = $config->getDatabase();
    $this->assertEquals('db', $database);
  }

  public function testGetPort()
  {
    $config = new DefaultDBConfig();
    $port = $config->getPort();
    $this->assertEquals('5432', $port);
  }
}

