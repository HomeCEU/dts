<?php


namespace HomeCEU\DTS\Persistence;

use HomeCEU\DTS\Persistence;
use HomeCEU\DTS\Repository\RecordNotFoundException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

abstract class InMemory implements Persistence {
  private array $data = [];

  abstract public function getTable(): string;
  abstract public function idColumns(): array;

  public function generateId(): string {
    return $this->uuid1()->toString();
  }

  public function persist(array $data): string {
    $this->data[$this->getIdFromData($data)] = $data;
    return $this->getIdFromData($data);
  }

  public function update(array $data): void {
    $this->data[$this->getIdFromData($data)] = $data;
  }

  public function retrieve(string $id, array $cols=['*']): array {
    if (!$this->has($id))
      throw new RecordNotFoundException("No record found {$id}");
    return $this->data[$id];
  }

  public function delete(string $id) {
    if (!$this->has($id))
      throw new RecordNotFoundException("No record found {$id}");
    unset($this->data[$id]);
  }

  protected function uuid1(): UuidInterface {
    return Uuid::uuid1();
  }

  private function getIdFromData(array $data): string {
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
  protected function has(string $id): bool {
    return array_key_exists($id, $this->data);
  }

  public function find(array $filter, array $cols=['*']): array {
    $matching = [];
    foreach ($this->data as $id=>$entity) {
      if ($this->matchesFilter($entity, $filter)) {
        array_push($matching, $entity);
      }
    }
    return $matching;
  }

  private function matchesFilter(array $entity, array $filter): bool {
    foreach ($filter as $k=>$v) {
      if (empty($entity[$k]) || $entity[$k] != $v) {
        return false;
      }
    }
    return true;
  }
}
