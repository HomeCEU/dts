<?php

namespace HomeCEU\DTS\Entity;


use HomeCEU\DTS\AbstractEntity;

class Template extends AbstractEntity {
  public string $id;
  public string $key;
  public string $docType;
  public ?string $name = '';
  public string $author;
  public \DateTime $createdAt;
  public string $body;
}
