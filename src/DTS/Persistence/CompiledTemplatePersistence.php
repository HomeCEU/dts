<?php declare(strict_types=1);


namespace HomeCEU\DTS\Persistence;


use HomeCEU\DTS\Db\Connection;

class CompiledTemplatePersistence extends AbstractPersistence {
  const TABLE = 'compiled_template';
  const ID_COL = 'template_id';

  private const MAP = [
      'templateId' => 'template_id',
      'body' => 'body',
      'createdAt' => 'created_at',
  ];

  public function __construct(Connection $db) {
    parent::__construct($db);
    $this->useKeyMap(self::MAP);
  }

  public function findBy(string $docType, string $key) {
    $row = $this->db->query("SELECT ct.* FROM compiled_template ct
                                JOIN template t
                                    ON t.template_id = ct.template_id
                                    AND t.doc_type = ?
                                    AND t.template_key = ?", $docType, $key
    )->fetch();
    return !empty($row) ? $this->hydrate($row) : null;
  }

  public function generateId(): string {
    $this->notImplemented(__METHOD__);
  }

  public function delete($id): void {
    $this->notImplemented(__METHOD__);
  }

  protected function notImplemented($method): void {
    $error = sprintf(
        "%s not implemented",
        $method
    );
    throw new \Exception($error);
  }
}
