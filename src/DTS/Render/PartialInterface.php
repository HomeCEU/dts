<?php


namespace HomeCEU\DTS\Render;


interface PartialInterface {
  public function get(string $parameter);
  public function toArray(): array;
}