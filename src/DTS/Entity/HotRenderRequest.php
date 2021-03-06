<?php declare(strict_types=1);


namespace HomeCEU\DTS\Entity;


use HomeCEU\DTS\AbstractEntity;

class HotRenderRequest extends AbstractEntity {
  public string $id;
  public string $template;
  public array $data;
  public \DateTime $createdAt;
}
