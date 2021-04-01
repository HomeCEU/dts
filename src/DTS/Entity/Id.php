<?php declare(strict_types=1);


namespace HomeCEU\DTS\Entity;


use Ramsey\Uuid\Uuid;

class Id {
  public static function create(): string {
    return Uuid::uuid1()->toString();
  }
}
