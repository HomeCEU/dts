<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\UseCase\Render;


use HomeCEU\DTS\Render\CompilationException;
use HomeCEU\DTS\Render\RenderFactory;
use HomeCEU\DTS\Repository\HotRenderRepository;
use HomeCEU\DTS\Repository\TemplateRepository;
use HomeCEU\DTS\UseCase\Exception\InvalidHotRenderRequestException;
use HomeCEU\DTS\UseCase\Render\AddHotRender;
use HomeCEU\DTS\UseCase\Render\AddHotRenderRequest;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;

class AddHotRenderTest extends TestCase {
  const EXAMPLE_DOCTYPE = 'example_doctype';

  private $useCase;
  private $hotRenderRequestPersistence;
  private $templatePersistence;

  protected function setUp(): void {
    parent::setUp();
    $this->hotRenderRequestPersistence = $this->fakePersistence('hotrender_request', 'requestId');
    $this->templatePersistence = $this->fakePersistence('template', 'templateId');

    $this->useCase = new AddHotRender(
        new HotRenderRepository($this->hotRenderRequestPersistence),
        new TemplateRepository($this->templatePersistence, $this->fakePersistence('compiled_template', 'templateId'))
    );
  }

  public function testInvalidRequest(): void {
    $this->expectException(InvalidHotRenderRequestException::class);
    $request = AddHotRenderRequest::fromState(['data' => ['name' => 'test']]);
    $this->useCase->add($request);
  }

  public function testAddSimpleTemplate(): void {
    $addRequest = $this->fakeAddRequest('{{ name }}', ['name' => 'test']);
    $renderRequest = $this->useCase->add($addRequest);

    $this->assertRequestPersisted($renderRequest);
    Assert::assertEquals("test", $this->renderHtml($renderRequest['template'], $renderRequest['data']));
  }

  public function testAddTemplateMissingPartialAndNoDocTypeProvided(): void {
    $addRequest = $this->fakeAddRequest('Hello, {{> a_partial }}!', []);
    $renderRequest = $this->useCase->add($addRequest);

    $this->assertRequestPersisted($renderRequest);
    Assert::assertEquals("Hello, !", $this->renderHtml($renderRequest['template']));
  }

  public function testAddTemplateWithPartials(): void {
    $partial = $this->fakeTemplate(self::EXAMPLE_DOCTYPE . '/partial', 'a_partial');
    $partial->body = 'world';
    $this->templatePersistence->persist($partial->toArray());
    $addRequest = $this->fakeAddRequest('Hello, {{> a_partial }}!', [], self::EXAMPLE_DOCTYPE);
    $renderRequest = $this->useCase->add($addRequest);

    $this->assertRequestPersisted($renderRequest);
    Assert::assertEquals("Hello, world!", $this->renderHtml($renderRequest['template']));
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
    Assert::assertEquals($renderRequest, $this->hotRenderRequestPersistence->retrieve($renderRequest['requestId']));
  }
}
