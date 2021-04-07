<?php declare(strict_types=1);


namespace HomeCEU\DTS\Entity;


use HomeCEU\DTS\AbstractEntity;

class Partial extends AbstractEntity {
  public string $id;
  public string $name;
  public string $docType;
  public string $body;
  public string $author;
  public array $metadata = [];
  public \DateTime $createdAt;
}
