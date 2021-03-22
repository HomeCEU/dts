<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase;


use HomeCEU\DTS\AbstractEntity;
use HomeCEU\DTS\UseCase\Exception\InvalidAddPartialRequestException;

class AddPartialRequest extends AbstractEntity {
  public string $docType;
  public string $body;
  public string $name;
  public string $author;

  public static function fromState(array $state): self {
    return parent::fromState($state)->validate();
  }

  protected function validate(): self {
    if (empty($this->docType)
        || empty($this->body)
        || empty($this->name)
        || empty($this->author)) {
      throw new InvalidAddPartialRequestException('Cannot Create Partial', self::keys());
    }
    return $this;
  }
}
