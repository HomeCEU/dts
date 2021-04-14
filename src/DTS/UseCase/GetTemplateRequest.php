<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase;


use HomeCEU\DTS\AbstractEntity;

class GetTemplateRequest extends AbstractEntity {
  public string $id;
  public string $docType;
  public string $key;

  public function isValid(): bool {
    return !empty($this->id) || (!empty($this->docType) && !empty($this->key));
  }
}
