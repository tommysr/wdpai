<?php

require_once __DIR__.'/../../Database.php';

class Repository
{
  protected $db;

  public function __construct()
  {
    $this->db = new Database();
    $this->db->connect();
  }
}