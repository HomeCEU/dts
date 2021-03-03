<?php


namespace HomeCEU\DTS\Persistence\InMemory;


use HomeCEU\DTS\Persistence\InMemory;

class DocDataPersistence extends InMemory {
  public function getTable() {
    return 'docdata';
  }

  public function idColumns(): array {
    return ['dataId'];
  }
}
