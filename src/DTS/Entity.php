<?php


namespace HomeCEU\DTS;


interface Entity {
  public static function fromState(array $state);
  public function toArray(): array;
  public function get(string $property);
}
