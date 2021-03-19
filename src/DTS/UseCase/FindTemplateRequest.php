<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase;


use HomeCEU\DTS\AbstractEntity;

class FindTemplateRequest extends AbstractEntity {
  public $type;
  public $key;
  public $search;

  public function isValid(): bool {
    return !empty($this->type);
  }
}
