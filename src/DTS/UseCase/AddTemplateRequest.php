<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase;


use HomeCEU\DTS\AbstractEntity;
use HomeCEU\DTS\UseCase\Exception\InvalidAddTemplateRequestException;

class AddTemplateRequest extends AbstractEntity {
  public string $docType;
  public string $key;
  public string $author;
  public string $body;

  public static function fromState(array $state): self {
    return parent::fromState($state)->validate();
  }

  protected function validate(): self {
    if (empty($this->docType)
        || empty($this->key)
        || empty($this->author)
        || empty($this->body)) {
      throw new InvalidAddTemplateRequestException("Required values: " . implode(', ', self::keys()));
    }
    return $this;
  }
}
