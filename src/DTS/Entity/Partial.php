<?php declare(strict_types=1);


namespace HomeCEU\DTS\Entity;


use HomeCEU\DTS\AbstractEntity;
use HomeCEU\DTS\Render\PartialInterface;

class Partial extends AbstractEntity implements PartialInterface {
  public string $id;
  public string $name;
  public string $docType;
  public string $body;
  public string $author;
  public array $metadata = [];
  public \DateTime $createdAt;
}
