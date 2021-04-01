<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase;


use HomeCEU\DTS\AbstractEntity;

class FindTemplateRequest extends AbstractEntity {
  public string $type;
  public ?string $key;
  public ?string $search;

  public function isValid(): bool {
    return !empty($this->type);
  }
}
