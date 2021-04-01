<?php declare(strict_types=1);


namespace HomeCEU\DTS\Entity;


use HomeCEU\DTS\AbstractEntity;

class CompiledTemplate extends AbstractEntity {
  public string $templateId;
  public string $body;
  public \DateTime $createdAt;
}
