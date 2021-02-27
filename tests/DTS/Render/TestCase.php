<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\Render;


use HomeCEU\DTS\Render\RenderFactory;
use HomeCEU\DTS\Render\TemplateCompiler;
use HomeCEU\Tests\DTS\TestCase as dtsTestCase;

class TestCase extends dtsTestCase {
  protected $compiler;

  protected function setUp(): void {
    $this->compiler = TemplateCompiler::create();
  }

  protected function compile($template): string {
    return $this->compiler->compile($template);
  }

  protected function renderHTML($compiledTemplate, $data = []): string {
    $path = RenderFactory::createHTML()->render($compiledTemplate, $data);
    return file_get_contents($path);
  }

  public function renderPDF($compiledTemplate, $data = []): string {
    return RenderFactory::createPDF()->render($compiledTemplate, $data);
  }
}
