<?php


namespace HomeCEU\DTS\Persistence;


use Exception;
use HomeCEU\DTS\Db\Connection;
use HomeCEU\DTS\Persistence;
use PDO;

class TemplatePersistence extends AbstractPersistence implements Persistence {
  const TABLE = 'template';
  const ID_COL = 'template_id';
  const HEAD_COLS = [
      'templateId', 'docType', 'templateKey',
      'name', 'author', 'createdAt'
  ];

  private array $map = [
      'templateId' => 'template_id',
      'docType' => 'doc_type',
      'templateKey' => 'template_key',
      'createdAt' => 'created_at'
  ];
  private string $isLatestVersionSQL = "
      t1.template_id = (
          SELECT t2.template_id
          FROM template t2
          WHERE t1.doc_type = t2.doc_type
            AND t1.template_key = t2.template_key
          ORDER BY created_at DESC LIMIT 1)";

  public function __construct(Connection $db) {
    parent::__construct($db);
    $this->useKeyMap($this->map);
  }

  public function delete(string $id): void {
    $this->db->deleteWhere(self::TABLE, ['template_id' => $id]);
  }

  public function filterByDoctype(string $type, $cols=self::HEAD_COLS) {
    $sql = $this->latestTemplatesSQL(...$cols)." AND doc_type=:type";
    $binds = ["type" => $type];
    $rows = $this->fetchAll($sql, $binds);
    return array_map([$this, 'hydrate'], $rows);
  }

  public function filterBySearchString(string $searchString, $cols=self::HEAD_COLS) {
    $andWhere = "AND CONCAT_WS(' ', doc_type, template_key, name, author) like :pattern";
    $pattern = '%'.str_replace(' ','%', $searchString).'%';
    $sql = $this->latestTemplatesSQL(...$cols).$andWhere;
    $binds = ['pattern' => $pattern];
    $rows = $this->fetchAll($sql, $binds);
    return array_map([$this, 'hydrate'], $rows);
  }

  public function latestVersions($cols=self::HEAD_COLS) {
    $sql = $this->latestTemplatesSQL(...$cols);
    $rows = $this->fetchAll($sql);
    return array_map([$this, 'hydrate'], $rows);
  }

  protected function latestTemplatesSQL(...$cols) {
    $colList = $this->selectColumns(...$cols);
    return "SELECT {$colList} FROM template t1
      WHERE {$this->isLatestVersionSQL}";
  }

  public function listDocTypes(): array {
    $sql = "SELECT doc_type AS docType, count(1) AS templateCount FROM template t1
      WHERE {$this->isLatestVersionSQL}
      GROUP BY doc_type";
    return $this->fetchAll($sql);
  }

  protected function fetchAll($sql, $binds=[]) {
    return $this->db->pdoQuery($sql, $binds)->fetchAll( PDO::FETCH_ASSOC);
  }
}
