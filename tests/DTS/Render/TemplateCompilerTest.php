<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\Render;


use HomeCEU\DTS\Render\CompilationException;
use HomeCEU\DTS\Render\Helper;
use HomeCEU\DTS\Render\Partial;
use HomeCEU\DTS\Render\TemplateCompiler;
use PHPUnit\Framework\Assert;

class TemplateCompilerTest extends TestCase {
  public function testCompileTemplate(): void {
    $data = ['placeholder' => 'password'];
    $template = $this->compile('{{ placeholder }}');
    $this->assertEquals($data['placeholder'], $this->renderHTML($template, $data));
  }

  public function testExpectedPartialNotProvided(): void {
    $this->expectException(CompilationException::class);
    $this->compiler->compile('{{> expected_partial }}');
  }

  public function testDisablePartialNotProvidedException(): void {
    $this->compiler->ignoreMissingPartials();
    $template = $this->compiler->compile('This, {{>expected_partial}}is a test');
    Assert::assertEquals('This, is a test', $this->renderHTML($template));
  }

  public function testProvidedPartialNotExpected(): void {
    $this->compiler->addPartial(new Partial('a_partial', 'some text'));
    $this->assertEquals('a template', $this->renderHTML($this->compiler->compile('a template')));
  }

  public function testCompileWithPartial(): void {
    $this->compiler->setPartials([new Partial('expected_partial', 'text')]);
    $this->assertEquals('text', $this->renderHTML($this->compile('{{> expected_partial }}')));
  }

  public function testCompileWithHelper(): void {
    $this->compiler->setHelpers([
        new Helper('upper', function ($val) {
          return strtoupper($val);
        })
    ]);
    $this->assertEquals('TEXT', $this->renderHTML($this->compile('{{upper var}}'), ['var' => 'text']));
  }
}
