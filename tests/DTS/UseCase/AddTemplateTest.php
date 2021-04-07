<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\UseCase;


use HomeCEU\DTS\Persistence;
use HomeCEU\DTS\Repository\PartialRepository;
use HomeCEU\DTS\Repository\TemplateRepository;
use HomeCEU\DTS\UseCase\AddTemplate;
use HomeCEU\DTS\UseCase\AddTemplateRequest;
use HomeCEU\DTS\UseCase\Exception\InvalidAddTemplateRequestException;
use HomeCEU\Tests\DTS\PartialTestTrait;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;

class AddTemplateTest extends TestCase {
  use PartialTestTrait;

  const TEST_DOCTYPE = 'test_doctype';

  private Persistence $templatePersistence;
  private Persistence $compiledTemplatePersistence;
  private Persistence $partialPersistence;
  private AddTemplate $useCase;

  protected function setUp(): void {
    parent::setUp();

    $this->templatePersistence = $this->fakePersistence('template', 'templateId');
    $this->compiledTemplatePersistence = $this->fakePersistence('compiled_template', 'templateId');
    $this->partialPersistence = $this->fakePersistence('partial', 'id');

    $templateRepository = new TemplateRepository(
        $this->templatePersistence,
        $this->compiledTemplatePersistence
    );
    $partialRepository = new PartialRepository($this->partialPersistence);
    $this->useCase = new AddTemplate($templateRepository, $partialRepository);
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
    $this->partialPersistence->persist($this->createSamplePartial(self::TEST_DOCTYPE, 'a_partial')->toArray());
    $this->partialPersistence->persist($this->createSamplePartial(self::TEST_DOCTYPE, 'another_partial')->toArray());

    $request = $this->createAddRequestWithBody('{{> a_partial }} {{> another_partial }}');
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
