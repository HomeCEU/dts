<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\UseCase;


use HomeCEU\DTS\Render\CompilationException;
use HomeCEU\DTS\Render\RenderFactory;
use HomeCEU\DTS\Repository\PartialRepository;
use HomeCEU\DTS\Repository\TemplateRepository;
use HomeCEU\DTS\UseCase\AddPartial;
use HomeCEU\DTS\UseCase\AddPartialRequest;
use HomeCEU\Tests\DTS\PartialTestTrait;
use HomeCEU\Tests\DTS\TemplateTestTrait;
use HomeCEU\Tests\DTS\TestCase;

class AddPartialTest extends TestCase {
  use PartialTestTrait;
  use TemplateTestTrait;

  private AddPartial $service;
  private PartialRepository $partialRepository;
  private TemplateRepository $templateRepository;

  protected function setUp(): void {
    parent::setUp();
    $this->partialRepository = new PartialRepository($this->fakePersistence('partial', 'id'));
    $this->templateRepository = new TemplateRepository(
        $this->fakePersistence('template', 'templateId'),
        $this->fakePersistence('compiled_template', 'templateId')
    );
    $this->service = new AddPartial($this->partialRepository, $this->templateRepository);
  }

  public function testAddPartialFromRequest(): void {
    $request = $this->createAddPartialRequest('DT', 'a_partial', 'body');
    $saved = $this->service->add($request);
    $found = $this->partialRepository->getById($saved->id);

    $this->assertEquals($found, $saved);
  }

  public function testAddingPartialCompilesTemplatesWithSameDocType(): void {
    $docType = 'DT';
    $request = $this->createAddPartialRequest($docType, 'a_partial', 'partial body');
    $template = $this->createSampleTemplate($docType, 'key', '{{> a_partial }}');
    $this->templateRepository->save($template);
    $this->templateRepository->saveCompiled($template, 'compiled_version');

    $this->service->add($request);
    $ct = $this->templateRepository->getCompiledTemplateById($template->templateId);

    $r = RenderFactory::createHTML();
    $this->assertEquals('partial body', file_get_contents($r->render($ct->body, [])));
  }

  public function testCompilationErrorProvidesTemplateData(): void {
    $request = $this->createAddPartialRequest('DT', 'a_partial', '{{#if name }}');
    $template = $this->createSampleTemplate('DT', 'key', '{{> a_partial }}');
    $this->templateRepository->save($template);
    $this->templateRepository->saveCompiled($template, 'compiled_version');

    try {
      $this->service->add($request);
    } catch (CompilationException $e) {
      $meta = $e->templateMetadata;
      $this->assertEquals($template->templateId, $meta[0]['template']['id']);
      $this->assertEquals($template->templateKey, $meta[0]['template']['key']);
    }
  }

  protected function createAddPartialRequest(string $docType, string $key, string $body): AddPartialRequest {
    $state = [
        'docType' => $docType,
        'name' => $key,
        'body' => $body,
        'author' => 'an_author',
        'metadata' => [
            'type' => 'generic'
        ]
    ];
    return AddPartialRequest::fromState($state);
  }
}
