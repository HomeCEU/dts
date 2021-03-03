<?php


namespace HomeCEU\DTS\Persistence;

use HomeCEU\DTS\Persistence;
use HomeCEU\DTS\Repository\RecordNotFoundException;
use Ramsey\Uuid\Uuid;

abstract class InMemory implements Persistence {
  private array $data = [];

  abstract public function getTable();
  abstract public function idColumns(): array;

  public function generateId() {
    return $this->uuid1()->toString();
  }

  public function persist($data) {
    $this->data[$this->getIdFromData($data)] = $data;
  }

  public function retrieve($id, array $cols=['*']) {
    if (!$this->has($id))
      throw new RecordNotFoundException("No record found {$id}");
    return $this->data[$id];
  }

  public function delete($id) {
    if (!$this->has($id))
      throw new RecordNotFoundException("No record found {$id}");
    unset($this->data[$id]);
  }

  protected function uuid1() {
    return Uuid::uuid1();
  }

  private function getIdFromData($data) {
    $id = [];
    foreach($this->idColumns() as $key) {
      array_push($id, $data[$key]);
    }
    return implode('-',$id);
  }

  /**
   * @param $id
   * @return bool
   */
  protected function has($id): bool {
    return array_key_exists($id, $this->data);
  }

  public function find(array $filter, array $cols=['*']) {
    $matching = [];
    foreach ($this->data as $id=>$entity) {
      if ($this->matchesFilter($entity, $filter)) {
        array_push($matching, $entity);
      }
    }
    return $matching;
  }

  private function matchesFilter(array $entity, array $filter) {
    foreach ($filter as $k=>$v) {
      if (empty($entity[$k]) || $entity[$k] != $v) {
        return false;
      }
    }
    return true;
  }
}
