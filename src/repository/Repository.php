<?php

require_once __DIR__ . '/../../Database.php';
require_once __DIR__ . '/../../DBConfig.php';

class Repository
{
  protected IDatabase $db;

  public function __construct(IDatabase $db = null, IDatabaseConfig $config = null)
  {
    $config = $config ?: new DefaultDBConfig();
    $this->db = $db ?: Database::getInstance($config);
  }
}