<?php declare(strict_types=1);


namespace HomeCEU\DTS\Entity;


use HomeCEU\DTS\AbstractEntity;
use HomeCEU\DTS\Render\PartialInterface;

class Partial extends AbstractEntity implements PartialInterface {
  public string $id;
  public string $key;
  public string $docType;
  public string $body;
  public string $author;
  public array $metadata = [];
  public \DateTime $createdAt;

  public function getKey(): string {
    return $this->key;
  }

  public function getBody(): string {
    return $this->body;
  }
}
