<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase\Render;


use HomeCEU\DTS\AbstractEntity;

class AddHotRenderRequest extends AbstractEntity {
  public string $template;
  public array $data;
  public ?string $docType;

  public function isValid(): bool {
    return !empty($this->template) && isset($this->data);
  }
}
