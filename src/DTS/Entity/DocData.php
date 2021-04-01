<?php


namespace HomeCEU\DTS\Entity;


use HomeCEU\DTS\AbstractEntity;

class DocData extends AbstractEntity {
  public string $dataId;
  public string $docType;
  public string $dataKey;
  public \DateTime $createdAt;
  public array $data;
}
