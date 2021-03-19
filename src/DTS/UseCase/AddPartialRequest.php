<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase;


use HomeCEU\DTS\AbstractEntity;

class AddPartialRequest extends AbstractEntity {
  public string $docType;
  public string $body;
  public string $name;
  public string $author;
}
