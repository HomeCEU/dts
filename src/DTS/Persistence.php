<?php


namespace HomeCEU\DTS;


interface Persistence {
  public function generateId(): string;
  public function persist(array $data): string;
  public function update(array $data): void;
  public function retrieve(string $id, array $cols=['*']): array;
  public function find(array $filter, array $cols=['*']): array;
  public function delete(string $id);
}
