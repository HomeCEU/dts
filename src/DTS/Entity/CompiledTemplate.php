<?php declare(strict_types=1);


namespace HomeCEU\DTS\Entity;


use HomeCEU\DTS\AbstractEntity;

class CompiledTemplate extends AbstractEntity {
  public string $templateId;
  public string $body;
  public \DateTime $createdAt;

  public static function fromState(array $state): self {
    return parent::fromState($state)->validate();
  }

  protected function validate(): self {
    if (empty($this->templateId)
        || empty($this->body)
        || empty($this->createdAt)) {
      throw new IncompleteEntityException('', self::keys());
    }
    return $this;
  }
}
