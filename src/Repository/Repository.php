<?php

namespace App\Repository;

use App\Database\IDatabase;
use App\Database\IDatabaseConfig;

use App\Database\DefaultDBConfig;
use App\Database\Database;

class Repository
{
  protected IDatabase $db;

  public function __construct(IDatabase $db = null, IDatabaseConfig $config = null)
  {
    $config = $config ?: new DefaultDBConfig();
    $this->db = $db ?: Database::getInstance($config);
  }
}