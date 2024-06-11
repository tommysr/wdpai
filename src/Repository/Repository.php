<?php

namespace App\Repository;

use App\Database\IDatabase;

class Repository
{
  protected IDatabase $db;

  public function __construct(IDatabase $db)
  {
    $this->db = $db;
  }
}