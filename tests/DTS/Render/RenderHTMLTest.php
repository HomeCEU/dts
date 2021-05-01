<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\Render;


use PHPUnit\Framework\Assert;

class RenderHTMLTest extends TestCase {
  public function testCreateHTML(): void {
    $data = ['name' => 'Tester'];
    $template = $this->compile('<h1>{{ name }}</h1>');
    Assert::assertEquals('<h1>Tester</h1>', $this->renderHTML($template, $data));
  }
}
