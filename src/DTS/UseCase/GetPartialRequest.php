<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase;


use HomeCEU\DTS\AbstractEntity;
use HomeCEU\DTS\UseCase\Exception\InvalidPartialRequestException;

class GetPartialRequest extends AbstractEntity {
  public string $id;
  public string $name;
  public string $docType;

  public static function fromState(array $state): self {
    return parent::fromState($state)->validate();
  }

  protected function validate(): self {
    if ($this->isMissingId() && $this->isMissingNameOrDoctype()) {
      throw new InvalidPartialRequestException('', self::keys());
    }
    return $this;
  }

  protected function isMissingNameOrDoctype(): bool {
    return empty($this->name) || empty($this->docType);
  }

  protected function isMissingId(): bool {
    return empty($this->id);
  }
}
