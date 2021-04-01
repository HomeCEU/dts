<?php declare(strict_types=1);


namespace HomeCEU\DTS\Persistence;


use HomeCEU\DTS\Db\Connection;
use HomeCEU\DTS\Persistence;

class HotRenderPersistence extends AbstractPersistence implements Persistence {
  const TABLE = 'hotrender_request';
  const ID_COL = 'request_id';

  private array $map = [
      'requestId' => 'request_id',
      'template' => 'template',
      'data' => 'data',
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
