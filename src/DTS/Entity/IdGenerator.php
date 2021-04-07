<?php declare(strict_types=1);


namespace HomeCEU\DTS\Entity;


use Ramsey\Uuid\Uuid;

class IdGenerator {
  public static function create(): string {
    return Uuid::uuid1()->toString();
  }
}
