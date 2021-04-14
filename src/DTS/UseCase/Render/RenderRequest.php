<?php


namespace HomeCEU\DTS\UseCase\Render;


use HomeCEU\DTS\AbstractEntity;

class RenderRequest extends AbstractEntity {
  public string $docType;
  public string $templateId;
  public string $templateKey;
  public string $dataId;
  public string $dataKey;
  public string $format;

  public function isValid(): bool {
    return $this->isValidTemplate() && $this->isValidDocData();
  }

  private function isValidTemplate(): bool {
    if (!empty($this->templateId)) return true;
    if ((!empty($this->docType) && !empty($this->templateKey))) return true;
    return false;
  }

  private function isValidDocData(): bool {
    if (!empty($this->dataId)) return true;
    if ((!empty($this->docType) && !empty($this->dataKey))) return true;
    return false;
  }
}
