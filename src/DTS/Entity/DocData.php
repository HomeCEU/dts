<?php


namespace HomeCEU\DTS\Entity;


use HomeCEU\DTS\AbstractEntity;

class DocData extends AbstractEntity {
  public string $id;
  public string $key;
  public string $docType;
  public \DateTime $createdAt;
  public array $data;
}
