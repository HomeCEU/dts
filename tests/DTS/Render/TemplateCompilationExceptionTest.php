<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\Render;


use HomeCEU\DTS\Render\CompilationException;

class TemplateCompilationExceptionTest extends TestCase {
  public function testUnclosedToken(): void {
    $t = '{{#if name }}';

    $this->expectException(CompilationException::class);
    $this->expectExceptionCode(CompilationException::ERROR_SYNTAX_ERROR);

    $this->compiler->compile($t);
  }

  public function testMissingPartial(): void {
    $t = '{{> missing_partial }}';

    $this->expectException(CompilationException::class);
    $this->expectExceptionCode(CompilationException::ERROR_MISSING_PARTIAL);
    $this->expectExceptionMessage('partials cannot be found');

    $this->compiler->compile($t);
  }
  
  public function testWrongVariableNaming(): void {
    $t = '{{> {{ invalid_variable_name }}';

    $this->expectException(CompilationException::class);
    $this->expectExceptionCode(CompilationException::ERROR_INVALID_VARIABLE);

    $this->compiler->compile($t);
  }
}
