<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\UseCase;


use HomeCEU\DTS\Entity\Template;
use HomeCEU\DTS\Repository\RecordNotFoundException;
use HomeCEU\DTS\Repository\TemplateRepository;
use HomeCEU\DTS\UseCase\Exception\InvalidGetTemplateRequestException;
use HomeCEU\DTS\UseCase\GetTemplate;
use HomeCEU\DTS\UseCase\GetTemplateRequest;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;

class GetTemplateTest extends TestCase {
  const DOC_TYPE_ENROLLMENT = 'enrollment';
  const DOC_TYPE_EXAMPLE = 'example';

  private $useCase;
  private $templateRepository;
  private $templatePersistence;

  protected function setUp(): void {
    parent::setUp();
    $this->templatePersistence = $this->fakePersistence('template', 'id');
    $compiledTemplatePersistence = $this->fakePersistence('compiled_template', 'templateId');

    $this->templateRepository = new TemplateRepository($this->templatePersistence, $compiledTemplatePersistence);
    $this->useCase = new GetTemplate($this->templateRepository);
  }

  public function testInvalidRequest(): void {
    $this->expectException(InvalidGetTemplateRequestException::class);
    $this->useCase->getTemplate(GetTemplateRequest::fromState([]));
  }

  public function testGetTemplateById(): void {
    $template = $this->fakeTemplate(self::DOC_TYPE_ENROLLMENT);
    $this->persistTemplates($template);
    Assert::assertEquals($template, $this->useCase->getTemplate(
        GetTemplateRequest::fromState(['id' => $template->id])
    ));
  }

  public function testGetTemplateByIdNotFound(): void {
    $this->expectException(RecordNotFoundException::class);
    $this->useCase->getTemplate(GetTemplateRequest::fromState(['id' => uniqid()]));
  }

  public function testGetTemplateByTypeAndKey(): void {
    $template = $this->fakeTemplate(self::DOC_TYPE_ENROLLMENT);
    $this->persistTemplates($template);
    Assert::assertEquals($template, $this->useCase->getTemplate(
        GetTemplateRequest::fromState(['docType' => self::DOC_TYPE_ENROLLMENT, 'key' => $template->key])
    ));
  }

  private function persistTemplates(Template ...$templates): void {
    foreach ($templates as $template) {
      $this->templatePersistence->persist($template->toArray());
    }
  }
}
