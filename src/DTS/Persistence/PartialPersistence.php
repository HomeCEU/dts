<?php declare(strict_types=1);


namespace HomeCEU\DTS\Persistence;


use HomeCEU\DTS\Db\Connection;

class PartialPersistence extends AbstractPersistence {
  const TABLE = 'partial';
  const ID_COL = 'partial_id';

  private array $map = [
      'id' => 'partial_id',
      'docType' => 'doc_type',
      'name' => 'name',
      'createdAt' => 'created_at',
      'metadata' => 'metadata',
      'body' => 'body'
  ];

  public function __construct(Connection $db) {
    parent::__construct($db);
    $this->useKeyMap($this->map);
  }
}
