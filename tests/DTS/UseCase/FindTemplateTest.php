<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\UseCase;


use HomeCEU\DTS\Entity\Template;
use HomeCEU\DTS\Persistence\InMemory;
use HomeCEU\DTS\Repository\TemplateRepository;
use HomeCEU\DTS\UseCase\Exception\InvalidGetTemplateRequestException;
use HomeCEU\DTS\UseCase\GetTemplate;
use HomeCEU\DTS\UseCase\FindTemplateRequest;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;

class FindTemplateTest extends TestCase {
  const DOC_TYPE_ENROLLMENT = 'enrollment';
  const DOC_TYPE_EXAMPLE = 'example';

  private GetTemplate $useCase;
  private InMemory $templatePersistence;

  protected function setUp(): void {
    parent::setUp();
    $this->templatePersistence = $this->fakePersistence('template', 'id');
    $compiledTemplatePersistence = $this->fakePersistence('compiled_template', 'templateId');
    $this->useCase = new GetTemplate(
        new TemplateRepository($this->templatePersistence, $compiledTemplatePersistence)
    );
  }

  public function testInvalidRequestThrowsException(): void {
    $this->expectException(InvalidGetTemplateRequestException::class);
    $r = FindTemplateRequest::fromState([]);
    $this->useCase->findTemplates($r);
  }

  public function testGetTemplateByType(): void {
    $t1 = $this->fakeTemplate(self::DOC_TYPE_ENROLLMENT);
    $t2 = $this->fakeTemplate(self::DOC_TYPE_ENROLLMENT);
    $t3 = $this->fakeTemplate(self::DOC_TYPE_EXAMPLE);
    $this->persistTemplates($t1, $t2, $t3);

    $r = FindTemplateRequest::fromState(['type' => self::DOC_TYPE_ENROLLMENT]);
    $result = $this->useCase->findTemplates($r);

    Assert::assertContainsEquals($t1, $result);
    Assert::assertContainsEquals($t2, $result);
    Assert::assertNotContainsEquals($t3, $result);
  }

  public function testGetTemplateByTypeAndKey(): void {
    $t1 = $this->fakeTemplate(self::DOC_TYPE_ENROLLMENT);
    $t2 = $this->fakeTemplate(self::DOC_TYPE_ENROLLMENT);
    $this->persistTemplates($t1, $t2);

    $result = $this->useCase->findTemplates(
        $this->createRequest(self::DOC_TYPE_ENROLLMENT, $t1->key)
    );

    Assert::assertContainsEquals($t1, $result);
    Assert::assertNotContainsEquals($t2, $result);
  }

  public function testGetOnlyNewestForEachTypeAndKeyCombination(): void {
    $sharedKey = uniqid();

    $t1 = $this->fakeTemplate(self::DOC_TYPE_ENROLLMENT, $sharedKey);
    $t2 = $this->fakeTemplate(self::DOC_TYPE_ENROLLMENT, $sharedKey);
    $t1->createdAt = new \DateTime('-1 day');
    $t2->createdAt = new \DateTime('-1 week');

    $this->persistTemplates($t1, $t2);
    $result = $this->useCase->findTemplates(
        $this->createRequest(self::DOC_TYPE_ENROLLMENT, $sharedKey)
    );
    Assert::assertContainsEquals($t1, $result);
    Assert::assertNotContainsEquals($t2, $result);
  }

  private function createRequest($type, $key = null, $search = null): FindTemplateRequest {
    return FindTemplateRequest::fromState([
        'type' => $type,
        'key' => $key,
        'search' => $search,
    ]);
  }

  private function persistTemplates(Template ...$templates): void {
    foreach ($templates as $template) {
      $this->templatePersistence->persist($template->toArray());
    }
  }
}
