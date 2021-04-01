<?php


namespace HomeCEU\DTS\Db;

use Exception;
use HomeCEU\DTS\Db\Config as DbConfig;
use Nette\Database\ResultSet;
use Nette\Database\Row;
use PDOStatement;

class Connection  extends \Nette\Database\Connection {
  public static function buildFromConfig(DbConfig $config, array $options = null): Connection {
    return new static(
        $config->dsn(),
        $config->user,
        $config->pass,
        $options
    );
  }

  public function pdoQuery(string $sql, array $binds=[]): PDOStatement {
    $sth = $this->_prepare($sql);
    return $this->_execute($sth, $binds);
  }

  private function _prepare(string $sql) {
    try {
      $sth = $this->getPdo()->prepare($sql);
      return $sth;
    }
    catch (\PDOException $e) {
      throw $this->prepareFailed($sql, $e);
    }
  }

  private function _execute(PDOStatement $sth, array $binds=[]): PDOStatement {
    try {
      $this->_bindParams($sth, $binds);

      if ($sth->execute() === false)
        $this->executeFailed($sth, $binds);

      return $sth;
    }
    catch (\PDOException $e) {
      throw $this->executeFailed($sth, $binds, $e);
    }
  }

  private function executeFailed(PDOStatement $sth, array $binds, \PDOException $prev=null): Exception {
    $sql = $sth->queryString;
    $bindParams = json_encode($binds);
    return new Exception(
        "Failed to execute {$sql} with binds {$bindParams}",
        0,
        $prev
    );
  }

  private function prepareFailed($sql, \PDOException $e): Exception {
    return new Exception(
        "Failed to prepare \"{$sql}\"\n  Error: {$e->getMessage()}",
        0,
        $e
    );
  }

  private function _bindParams(PDOStatement $sth, $binds) {
    foreach ($binds as $name=>$value) {
      $sth->bindParam($this->bindParamName($name), $value);
    }
  }

  private function bindParamName($name): string {
    return preg_match("/^:.+/", $name) ? $name : ":{$name}";
  }

  public function selectFirst($table, $itemString, array $where): ?Row {
    return $this->selectWhere($table, $itemString, $where)->fetch();
  }

  public function selectWhere($table, $itemString, array $where): ResultSet {
    return $this->query("SELECT {$itemString} FROM {$table} WHERE", $where);
  }

  public function insert($table, array ...$rows): string {
    $this->query("INSERT INTO {$table}", $rows);
    return $this->getInsertId();
  }

  public function deleteWhere($table, array $where): ResultSet {
    return $this->query("DELETE FROM {$table} WHERE ?", $where);
  }

  public function count(string $table, string $whereString, array $binds=[]) {
    return $this->pdoQuery(
        "SELECT count(1) FROM {$table} WHERE {$whereString}",
        $binds
    )->fetchColumn();
  }

  public function createTable(string $table, ...$params) {
    $data = "\n  ".implode(",\n  ", $params)."\n";
    $sql = "CREATE TABLE {$table} ({$data})";
    $this->pdoQuery($sql);
  }
}
