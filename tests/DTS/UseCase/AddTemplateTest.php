<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\UseCase;


use HomeCEU\DTS\Persistence\InMemory;
use HomeCEU\DTS\Repository\TemplateRepository;
use HomeCEU\DTS\UseCase\AddTemplate;
use HomeCEU\DTS\UseCase\AddTemplateRequest;
use HomeCEU\DTS\UseCase\InvalidAddTemplateRequestException;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;

class AddTemplateTest extends TestCase {
  const TEST_DOCTYPE = 'test_doctype';
  private InMemory $templatePersistence;
  private InMemory $compiledTemplatePersistence;
  private AddTemplate $useCase;

  protected function setUp(): void {
    parent::setUp();

    $this->templatePersistence = $this->fakePersistence('template', 'templateId');
    $this->compiledTemplatePersistence = $this->fakePersistence('compiled_template', 'templateId');
    $this->useCase = new AddTemplate(new TemplateRepository(
        $this->templatePersistence,
        $this->compiledTemplatePersistence
    ));
  }

  public function testAddTemplateInvalidRequest(): void {
    $this->expectException(InvalidAddTemplateRequestException::class);
    $this->useCase->addTemplate(AddTemplateRequest::fromState([]));
  }

  public function testAddBasicTemplate(): void {
    $request = $this->createAddRequestWithBody('Hi, {{ name }}!');
    $template = $this->useCase->addTemplate($request);

    Assert::assertEquals($template->toArray(), $this->templatePersistence->retrieve($template->templateId));
    Assert::assertNotEmpty($this->compiledTemplatePersistence->retrieve($template->templateId));
  }

  public function testAddTemplateWithPartials(): void {
    $this->templatePersistence->persist($this->fakeTemplate(self::TEST_DOCTYPE . '/partial', 'a_partial')->toArray());

    $request = $this->createAddRequestWithBody('{{> a_partial }}');
    $template = $this->useCase->addTemplate($request);

    Assert::assertEquals($template->toArray(), $this->templatePersistence->retrieve($template->templateId));
    Assert::assertNotEmpty($this->compiledTemplatePersistence->retrieve($template->templateId));
  }

  public function testAddTemplateWithImages(): void {
    $this->templatePersistence->persist($this->fakeTemplate(self::TEST_DOCTYPE . '/image', 'image.png')->toArray());

    $request = $this->createAddRequestWithBody('{{> image.png }}');
    $template = $this->useCase->addTemplate($request);

    Assert::assertEquals($template->toArray(), $this->templatePersistence->retrieve($template->templateId));
    Assert::assertNotEmpty($this->compiledTemplatePersistence->retrieve($template->templateId));
  }

  private function createAddRequestWithBody(string $body): AddTemplateRequest {
    return AddTemplateRequest::fromState([
        'docType' => self::TEST_DOCTYPE,
        'templateKey' => uniqid('key_'),
        'author' => 'Author',
        'body' => $body
    ]);
  }
}
