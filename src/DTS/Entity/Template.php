<?php

namespace HomeCEU\DTS\Entity;


use HomeCEU\DTS\AbstractEntity;

class Template extends AbstractEntity {
  public string $templateId;
  public string $templateKey;
  public string $docType;
  public ?string $name = '';
  public string $author;
  public \DateTime $createdAt;
  public string $body;

  protected static function keys(): array {
    return [
        'templateId',
        'docType',
        'templateKey',
        'createdAt',
        'name',
        'author',
        'body'
    ];
  }
}
