<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\Render;

use HomeCEU\DTS\Render\RenderFactory;
use HomeCEU\DTS\Render\RenderHTML;
use HomeCEU\DTS\Render\RenderPDF;
use PHPUnit\Framework\Assert;

class RenderFactoryTest extends TestCase {
  public function testCreateHTMLRenderer(): void {
    $r = RenderFactory::createHTML();
    Assert::assertInstanceOf(RenderHTML::class, $r);
  }

  public function testCreatePDFRenderer(): void {
      $r = RenderFactory::createPDF();
      Assert::assertInstanceOf(RenderPDF::class, $r);
  }
}
