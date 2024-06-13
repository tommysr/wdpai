<?php
use PHPUnit\Framework\TestCase;
use App\Database\Database;
use App\Database\IDatabaseConfig;

class DatabaseTest extends TestCase
{
    private $dbConfigMock;

    protected function setUp(): void
    {
        // Mock IDatabaseConfig
        $this->dbConfigMock = $this->createMock(IDatabaseConfig::class);
        $this->dbConfigMock->method('getHost')->willReturn('localhost');
        $this->dbConfigMock->method('getPort')->willReturn('5432'); // Example port
        $this->dbConfigMock->method('getDatabase')->willReturn('test_db');
        $this->dbConfigMock->method('getUsername')->willReturn('test_user');
        $this->dbConfigMock->method('getPassword')->willReturn('test_password');
    }

    public function testGetInstance()
    {
        $dbInstance1 = Database::getInstance($this->dbConfigMock);
        $dbInstance2 = Database::getInstance($this->dbConfigMock);

        $this->assertInstanceOf(Database::class, $dbInstance1);
        $this->assertSame($dbInstance1, $dbInstance2);
    }
}
