<?php
namespace App\Database;

use PDO;
use PDOException;

class Database implements IDatabase
{
    private ?PDO $connection = null;
    private static ?IDatabase $instance = null;
    private IDatabaseConfig $config;

    private function __construct(IDatabaseConfig $config)
    {
        $this->config = $config;
    }

    public static function getInstance(IDatabaseConfig $config): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new static($config);
        }
        return self::$instance;
    }

    public function connect(): PDO
    {
        if ($this->connection !== null) {
            return $this->connection;
        }

        try {

            $this->connection = new PDO(
                "pgsql:host={$this->config->getHost()};port={$this->config->getPort()};dbname={$this->config->getDatabase()}",
                $this->config->getUsername(),
                $this->config->getPassword(),
                ["sslmode" => "prefer"]
            );

            // set the PDO error mode to exception
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            throw new \RuntimeException('Database connection error: ' . $e->getMessage());
        }


        return $this->connection;
    }
}