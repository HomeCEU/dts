<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase\Render;


use HomeCEU\DTS\AbstractEntity;
use HomeCEU\DTS\UseCase\Exception\InvalidRequestException;

class AddHotRenderRequest extends AbstractEntity {
  public string $template;
  public array $data;
  public ?string $docType;

  public static function fromState(array $state): self {
    return parent::fromState($state)->validate();
  }

  public function validate(): self {
    if (empty($this->template) || !isset($this->data)) {
      throw new InvalidRequestException('', ['template', 'data']);
    }
    return $this;
  }
}
