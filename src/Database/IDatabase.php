<?php

namespace App\Database;
use PDO;

interface IDatabase
{
    public function connect(): PDO;
}
