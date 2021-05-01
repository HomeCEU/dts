<?php


namespace HomeCEU\Tests\DTS\Db;

use Exception;
use HomeCEU\DTS\Db\Connection;
use HomeCEU\DTS\Db\Config as DbConfig;

class ConnectionTest extends TestCase {
  private string $table;
  private Connection $connection;

  /** @throws Exception */
  protected function setUp(): void {
    $this->connection = Connection::buildFromConfig(DbConfig::sqlite());
    $this->initRolesTable();
  }

  /** @throws Exception */
  public function testPdoQueryPrepareFailure(): void {
    $this->expectException(Exception::class);
    $this->connection->pdoQuery("select * from {$this->table} where foo=:foo", ['foo' => 'bar']);
  }

  /** @throws Exception */
  public function testPdoQueryExecuteFailure(): void {
    $this->expectException(Exception::class);
    $this->connection->pdoQuery("select * from {$this->table} where role=:foo", ['a' => 'bar']);
  }

  /** @throws Exception */
  public function testDelete(): void {
    $this->connection->deleteWhere($this->table, ['role' => 'admin']);
    $count = $this->connection->count($this->table, "role=:role", ['role' => 'admin']);

    $this->assertEquals(0, $count);
  }

  public function testSelectFirst(): void {
    $this->connection->insert(
        $this->table,
        ['role' => 'admin', 'description' => 'system administrator'],
        ['role' => 'users', 'description' => 'foo']
    );

    $row = $this->connection->selectFirst(
        $this->table,
        'role, description',
        ['role' => 'admin']
    );

    $this->assertEquals('admin', $row->role);
    $this->assertEquals('system administrator', $row->description);
  }

  public function testUpdate(): void {
    $this->connection->insert($this->table, ['role' => 'admin', 'description' => 'system administrator']);
    $this->connection->update($this->table, ['description' => 'sys admin'], ['role' => 'admin']);

    $row = $this->connection->selectWhere(
        $this->table,
        'role, description',
        ['role' => 'admin']
    )->fetch();
    $this->assertEquals('sys admin', $row->description);
  }

  /** @throws Exception */
  private function initRolesTable(): void {
    $this->table = 'roles';
    $fields = [
        'role VARCHAR(32)',
        'description VARCHAR(128)',
        'PRIMARY KEY(role)'
    ];
    $this->connection->createTable($this->table, ...$fields);
  }
}
