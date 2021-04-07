<?php


namespace HomeCEU\DTS\Render;


use HomeCEU\DTS\Entity\GetPropertyTrait;

class Partial implements PartialInterface {
  use GetPropertyTrait;

  public string $name;
  public string $body;

  public function __construct(string $name, string $body) {
    $this->name = $name;
    $this->body = $body;
  }

  public function toArray(): array {
    return ['name' => $this->name, 'body' => $this->body];
  }
}
