<?php declare(strict_types=1);


namespace HomeCEU\DTS\Render;


use LightnCandy\Flags;
use LightnCandy\LightnCandy;

class TemplateCompiler {
  private int $flags = Flags::FLAG_HANDLEBARS;
  private array $helpers = [];
  private array $partials = [];

  private function __construct() {
    $this->addHelper(TemplateHelpers::equal());
  }

  public static function create(): self {
    return new self();
  }

  public function setHelpers(array $helpers): self {
    $this->helpers = [];
    foreach ($helpers as $helper) {
      $this->addHelper($helper);
    }
    return $this;
  }

  public function addHelper(Helper $helper): self {
    $this->helpers[$helper->name] = $helper->func;
    return $this;
  }

  public function setPartials(array $partials): self {
    $this->partials = [];
    foreach ($partials as $partial) {
      $this->checkIsPartial($partial);
      $this->addPartial($partial);
    }
    return $this;
  }

  public function addPartial(PartialInterface $partial): self {
    $this->partials[$partial->getKey()] = $partial->body;
    return $this;
  }

  public function ignoreMissingPartials(): self {
    $this->flags |= Flags::FLAG_ERROR_SKIPPARTIAL;
    return $this;
  }

  public function compile(string $template): string {
    try {
      $options = [
          'flags' => $this->flags | Flags::FLAG_ERROR_EXCEPTION,
          'helpers' => $this->helpers,
          'partials' => $this->partials,
      ];
      return LightnCandy::compile($template, $options);
    } catch (\Exception $e) {
      throw CompilationException::fromException($e);
    }
  }

  protected function checkIsPartial($partial): void {
    if (!($partial instanceof PartialInterface)) {
      throw new \TypeError('$partials must be an array of PartialInterface objects');
    }
  }
}
