<?php


namespace HomeCEU\DTS\Persistence;


use HomeCEU\DTS\Db\Connection;
use HomeCEU\DTS\Persistence;
use Ramsey\Uuid\Uuid;

abstract class AbstractPersistence implements Persistence {
  /** @var array */
  private $hydratedToDbMap;
  /** @var array */
  private $dbToHydratedMap;

  /** @var Connection */
  protected $db;

  public function __construct(Connection $db) {
    $this->db = $db;
  }

  public function generateId() {
    return Uuid::uuid1();
  }

  public function persist($entity) {
    $this->db->insert(static::TABLE, $this->flatten($entity));
  }

  public function retrieve($id, array $cols=['*']) {
    $row = $this->db->selectWhere(
        static::TABLE,
        $this->selectColumns(...$cols),
        [static::ID_COL => $id]
    )->fetch();
    if (is_null($row))
      throw new \Exception("Cannot retrieve entity with id: {$id} (not found)");
    return $this->hydrate($row);
  }

  public function find(array $filter, $cols=['*']) {
    $where = $this->flatten($filter); // changes keys to snake_case
    $rows = $this->db->selectWhere(
        static::TABLE,
        $this->selectColumns(...$cols),
        $where
    )->fetchAll();
    return array_map([$this, 'hydrate'], $rows);
  }

  /**
   * @param array $map map of hydratedKey => db_key
   */
  public function useKeyMap(array $map) {
    $this->hydratedToDbMap = $map;
    $this->dbToHydratedMap = array_flip($map);
  }

  protected function hydratedKey($db_key) {
    if (array_key_exists($db_key, $this->dbToHydratedMap)) {
      return $this->dbToHydratedMap[$db_key];
    }
    return $db_key;
  }
  protected function dbKey($hydratedKey) {
    if (array_key_exists($hydratedKey, $this->hydratedToDbMap)) {
      return $this->hydratedToDbMap[$hydratedKey];
    }
    return $hydratedKey;
  }

  public function flatten(array $entity) {
    $result = array();

    foreach ($entity as $k => $v) {
      if ($v instanceof \DateTime) {
        $value = $v->format('Y-m-d H:i:s');
      } elseif (is_array($v)) {
        $value = json_encode($v);
      } else {
        $value = $v;
      }
      $key = $this->dbKey($k);
      $result[$key] = $value;
    }

    return $result;
  }

  public function hydrate($entity) {
    $result = array();

    foreach ($entity as $k => $v) {
      $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $v);
      if ($dateTime !== FALSE) {
        $value = $dateTime;
      } elseif ($this->isJson($v)) {
        $value = $this->jsonDecodeAsArray($v);
      } else {
        $value = $v;
      }
      $key = $this->hydratedKey($k);
      $result[$key] = $value;
    }

    return $result;
  }

  private function isJson($v) {
    return is_array(json_decode($v, true));
  }

  private function jsonDecodeAsArray($json): array {
    return json_decode($json, true);
  }

  protected function selectColumns(...$cols) {
    $selectedCols = [];
    foreach ($cols as $alias) {
      array_push($selectedCols, $this->dbKey($alias));
    }
    return implode(', ', $selectedCols);
  }
}
