<?php


namespace HomeCEU\DTS;


interface Persistence {
  public function generateId(): string;
  public function persist($data): string;
  public function retrieve($id, array $cols=['*']): array;
  public function find(array $filter, array $cols=['*']): array;
  public function delete($id);
}
