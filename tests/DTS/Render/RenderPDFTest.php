<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\Render;


use PHPUnit\Framework\Assert;

class RenderPDFTest extends TestCase {
  public function testCreatePDF(): void {
    $data = ['name' => 'Peter Parker'];
    $template = $this->compile('{{ name }}');

    $path = $this->renderPDF($template, $data);
    Assert::assertFileExists($path);
    Assert::assertEquals('application/pdf', mime_content_type($path));
  }
}
