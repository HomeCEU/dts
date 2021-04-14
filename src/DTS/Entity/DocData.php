<?php


namespace HomeCEU\DTS\Entity;


use HomeCEU\DTS\AbstractEntity;

class DocData extends AbstractEntity {
  public string $id;
  public string $docType;
  public string $key;
  public \DateTime $createdAt;
  public array $data;
}
