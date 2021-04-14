<?php


namespace HomeCEU\DTS\Render;


use HomeCEU\DTS\AbstractEntity;

class Partial extends AbstractEntity implements PartialInterface {
  public string $name;
  public string $body;

  public function __construct(string $name, string $body) {
    $this->name = $name;
    $this->body = $body;
  }

  public function toArray(): array {
    return ['name' => $this->name, 'body' => $this->body];
  }

  public function getName(): string {
    return $this->name;
  }

  public function getBody(): string {
    return $this->body;
  }
}
