<?php
namespace App\Database;
use PDO;

class Database implements IDatabase
{
    private ?PDO $connection = null;
    private static IDatabase $instance;
    private IDatabaseConfig $config;

    protected function __construct(IDatabaseConfig $config)
    {
        $this->$config = $config;
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

        $connection = new PDO(
            "pgsql:host={$this->config->getHost()};port={$this->config->getPort()};dbname={$this->config->getDatabase()}",
            $this->config->getUsername(),
            $this->config->getPassword(),
            ["sslmode" => "prefer"]
        );

        // set the PDO error mode to exception
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $connection;
    }
}