<?php declare(strict_types=1);


namespace HomeCEU\DTS\Api\Partial;


use HomeCEU\DTS\AbstractEntity;

class AddPartialResponse extends AbstractEntity {
  public string $id;
  public string $key;
  public string $docType;
  public string $author;
  public array $metadata;
  public \DateTime $createdAt;
  public string $bodyUri;
}
