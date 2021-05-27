<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\UseCase\Render;


use HomeCEU\DTS\Persistence;
use HomeCEU\DTS\Render\CompilationException;
use HomeCEU\DTS\Render\RenderFactory;
use HomeCEU\DTS\Repository\HotRenderRepository;
use HomeCEU\DTS\Repository\PartialRepository;
use HomeCEU\DTS\UseCase\Render\AddHotRender;
use HomeCEU\DTS\UseCase\Render\AddHotRenderRequest;
use HomeCEU\Tests\DTS\PartialTestTrait;
use HomeCEU\Tests\DTS\TestCase;

class AddHotRenderTest extends TestCase {
  use PartialTestTrait;

  const EXAMPLE_DOCTYPE = 'example_doctype';

  private AddHotRender $useCase;
  private Persistence $hotRenderRequestPersistence;
  private Persistence $partialPersistence;

  protected function setUp(): void {
    parent::setUp();
    $this->hotRenderRequestPersistence = $this->fakePersistence('hotrender_request', 'id');
    $this->partialPersistence = $this->fakePersistence('partial', 'id');

    $this->useCase = new AddHotRender(
        new HotRenderRepository($this->hotRenderRequestPersistence),
        new PartialRepository($this->partialPersistence)
    );
  }

  public function testAddSimpleTemplate(): void {
    $addRequest = $this->fakeAddRequest('{{ name }}', ['name' => 'test']);
    $renderRequest = $this->useCase->add($addRequest);

    $this->assertRequestPersisted($renderRequest);
    $this->assertEquals("test", $this->renderHtml($renderRequest['template'], $renderRequest['data']));
  }

  public function testAddTemplateMissingPartialAndNoDocTypeProvided(): void {
    $addRequest = $this->fakeAddRequest('Hello, {{> a_partial }}!', []);
    $renderRequest = $this->useCase->add($addRequest);

    $this->assertRequestPersisted($renderRequest);
    $this->assertEquals("Hello, !", $this->renderHtml($renderRequest['template']));
  }

  public function testAddTemplateWithPartials(): void {
    $partial = $this->createSamplePartial(self::EXAMPLE_DOCTYPE, 'a_partial');
    $this->partialPersistence->persist($partial->toArray());
    $addRequest = $this->fakeAddRequest('Hello, {{> a_partial }}!', [], self::EXAMPLE_DOCTYPE);
    $renderRequest = $this->useCase->add($addRequest);

    $this->assertRequestPersisted($renderRequest);
    $this->assertEquals("Hello, {$partial->getBody()}!", $this->renderHtml($renderRequest['template']));
  }

  public function testAddTemplateMissingPartials(): void {
    $this->expectException(CompilationException::class);
    $addRequest = $this->fakeAddRequest('Hello, {{> a_partial }}!', [], self::EXAMPLE_DOCTYPE);
    $this->useCase->add($addRequest);
  }

  protected function fakeAddRequest(string $template, array $data, string $docType = ''): AddHotRenderRequest {
    $request = AddHotRenderRequest::fromState(['template' => $template, 'data' => $data]);
    if (!empty($docType)) {
      $request->docType = $docType;
    }
    return $request;
  }

  private function renderHtml($template, $data = []) {
    return file_get_contents(RenderFactory::createHTML()->render($template, $data));
  }

  private function assertRequestPersisted(array $renderRequest) {
    $this->assertEquals($renderRequest, $this->hotRenderRequestPersistence->retrieve($renderRequest['id']));
  }
}
