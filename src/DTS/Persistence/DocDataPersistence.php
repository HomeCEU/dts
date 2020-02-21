<?php


namespace HomeCEU\DTS\Persistence;


use HomeCEU\DTS\Db\Connection;
use HomeCEU\DTS\Persistence;

class DocDataPersistence extends AbstractPersistence implements Persistence {
  const TABLE = 'docdata';
  const ID_COL = 'data_id';

  private $map = [
      'dataId' => 'data_id',
      'docType' => 'doc_type',
      'dataKey' => 'data_key',
      'createdAt' => 'created_at'
  ];

  public function __construct(Connection $db) {
    parent::__construct($db);
    $this->useKeyMap($this->map);
  }

  public function delete($id) {
    $error = sprintf(
        "%s not implemented",
        __METHOD__
    );
    throw new \Exception($error);
  }
}