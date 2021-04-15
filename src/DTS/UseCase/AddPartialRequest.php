<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase;


use HomeCEU\DTS\AbstractEntity;
use HomeCEU\DTS\UseCase\Exception\InvalidAddPartialRequestException;

class AddPartialRequest extends AbstractEntity {
  public string $docType;
  public string $body;
  public string $key;
  public string $author;
  public array $metadata = [];

  public static function fromState(array $state): self {
    return parent::fromState($state)->validate();
  }

  protected function validate(): self {
    if (empty($this->docType)
        || empty($this->key)
        || empty($this->author)
        || !isset($this->body)) {
      throw new InvalidAddPartialRequestException('Cannot Create Partial', self::keys());
    }
    return $this;
  }
}
