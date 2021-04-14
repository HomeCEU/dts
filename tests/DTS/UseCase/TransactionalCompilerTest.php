<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\UseCase;


use HomeCEU\DTS\Entity\CompiledTemplate;
use HomeCEU\DTS\Entity\Template;
use HomeCEU\DTS\Render\CompilationException;
use HomeCEU\DTS\Render\RenderFactory;
use HomeCEU\DTS\Repository\PartialRepository;
use HomeCEU\DTS\Repository\TemplateRepository;
use HomeCEU\DTS\UseCase\TransactionalCompiler;
use HomeCEU\Tests\DTS\PartialTestTrait;
use HomeCEU\Tests\DTS\TemplateTestTrait;
use HomeCEU\Tests\DTS\TestCase;

class TransactionalCompilerTest extends TestCase {
  use TemplateTestTrait;
  use PartialTestTrait;

  private TransactionalCompiler $compiler;
  private TemplateRepository $templateRepository;
  private PartialRepository $partialRepository;

  protected function setUp(): void {
    parent::setUp();

    $this->templateRepository = new TemplateRepository(
        $this->fakePersistence('template', 'templateId'),
        $this->fakePersistence('compiled_template', 'templateId')
    );
    $this->partialRepository = new PartialRepository($this->fakePersistence('partial', 'id'));
    $this->compiler = new TransactionalCompiler($this->templateRepository, $this->partialRepository);
  }

  public function testCompileAllTemplatesForDocType(): void {
    $docType = 'DT';
    $template = $this->createSampleTemplate($docType, __FUNCTION__, '{{ name }}');
    $template2 = $this->createSampleTemplate($docType, __FUNCTION__ . uniqid(), '{{ age }}');
    $this->templateRepository->save($template);
    $this->templateRepository->save($template2);

    $this->compiler->compileAllTemplatesForDocType($docType);
    $this->assertNotEmpty($this->findCompiled($template));
    $this->assertNotEmpty($this->findCompiled($template2));
  }

  public function testWithPartials(): void {
    $docType = 'DT';
    $template = $this->createSampleTemplate($docType, uniqid(), '{{> a_partial }}');
    $partial = $this->createSamplePartial($docType, 'a_partial');
    $this->templateRepository->save($template);
    $this->partialRepository->save($partial);

    $this->compiler->compileAllTemplatesForDocType($docType);
    $compiledTemplate = $this->findCompiled($template);
    $rendered = RenderFactory::createHTML()->render($compiledTemplate->body, []);

    $this->assertEquals($partial->get('body'), file_get_contents($rendered));
  }

  public function testMissingPartial(): void {
    $docType = 'DT';
    $template = $this->createSampleTemplate($docType, uniqid(), '{{> a_partial }}');
    $this->templateRepository->save($template);

    try {
      $this->compiler->compileAllTemplatesForDocType($docType);
    } catch (CompilationException $e) {
      $this->assertEquals($template->templateId, $e->errors[0]['template']['id']);
      $this->assertEquals($template->templateKey, $e->errors[0]['template']['key']);
      $this->assertTrue(in_array('a_partial', $e->errors[0]['template']['partials']));
    }
  }

  public function testCompileAllTemplatesWithBadTemplate(): void {
    $docType = 'DT';
    // unclosed if statement (will trigger compilation error)
    $template = $this->createSampleTemplate($docType, __FUNCTION__, '{{#if name }}');
    $this->templateRepository->save($template);
    try {
      $this->compiler->compileAllTemplatesForDocType($docType);
    } catch (CompilationException $e) {
      $this->assertEquals($template->templateId, $e->errors[0]['template']['id']);
      $this->assertEquals($template->templateKey, $e->errors[0]['template']['key']);
    }
  }

  private function findCompiled(Template $template): CompiledTemplate {
    return $this->templateRepository->getCompiledTemplateById($template->templateId);
  }
}
