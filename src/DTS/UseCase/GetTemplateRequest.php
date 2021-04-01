<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase;


use HomeCEU\DTS\AbstractEntity;

class GetTemplateRequest extends AbstractEntity {
  public string $templateId;
  public string $docType;
  public string $templateKey;

  public function isValid(): bool {
    return !empty($this->templateId) || (!empty($this->docType) && !empty($this->templateKey));
  }
}
