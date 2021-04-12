<?php declare(strict_types=1);


namespace HomeCEU\DTS\Render;


class CompilationException extends \Exception {
  public array $templateMetadata;

  public function __construct($message = "", array $templateMetadata = []) {
    $this->message = $message;
    $this->templateMetadata = $templateMetadata;
  }
}
