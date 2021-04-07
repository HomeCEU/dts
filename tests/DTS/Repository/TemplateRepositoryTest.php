<?php

namespace HomeCEU\Tests\DTS\Repository;

use DateTime;
use HomeCEU\DTS\Db;
use HomeCEU\DTS\Entity\CompiledTemplate;
use HomeCEU\DTS\Entity\Template;
use HomeCEU\DTS\Persistence\CompiledTemplatePersistence;
use HomeCEU\DTS\Persistence\TemplatePersistence;
use HomeCEU\DTS\Render\Image;
use HomeCEU\DTS\Render\Partial;
use HomeCEU\DTS\Repository\RecordNotFoundException;
use HomeCEU\DTS\Repository\TemplateRepository;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;

class TemplateRepositoryTest extends TestCase {
  /** @var Db\Connection */
  private $db;

  /** @var TemplatePersistence */
  private $p;

  /** @var CompiledTemplatePersistence */
  private $ctp;

  /** @var TemplateRepository */
  private $repo;

  private $docType;

  protected function setUp(): void {
    parent::setUp();
    $this->db = Db::connection();
    $this->db->beginTransaction();

    $this->p = new TemplatePersistence($this->db);
    $this->ctp = new CompiledTemplatePersistence($this->db);
    $this->repo = new TemplateRepository($this->p, $this->ctp);
    $this->docType = 'TemplateRepositoryTest-' . time();
  }

  protected function tearDown(): void {
    parent::tearDown();
    $this->db->rollBack();
  }

  protected function newPersistedTemplate(array $overwrite): Template {
    if (empty($overwrite['docType'])) {
      $overwrite['docType'] = $this->docType;
    }

    $t = $this->newTemplate($overwrite);
    $this->p->persist($t->toArray());
    return $t;
  }

  public function testCreateNewTemplate(): void {
    $type = 'type';
    $key = 'key';
    $author = 'author';
    $body = 'body';

    $template = $this->repo->createNewTemplate($type, $key, $author, $body);
    Assert::assertSame($type, $template->docType);
    Assert::assertSame($key, $template->templateKey);
    Assert::assertSame($author, $template->author);
    Assert::assertSame($body, $template->body);
    Assert::assertNotEmpty($template->templateId);
    Assert::assertNotEmpty($template->createdAt);
  }

  public function testNewCompiledTemplate(): void {
    $body = "<?php /* compiled template */ ?>";
    $template = $this->repo->createNewTemplate('T', 'K', 'A', 'B');

    $compiled = $this->repo->createNewCompiledTemplate($template, $body);

    Assert::assertSame($template->templateId, $compiled->templateId);
    Assert::assertSame($body, $compiled->body);
    Assert::assertNotEmpty($compiled->createdAt);
  }

  public function testAddCompiledTemplate(): void {
    $template = $this->repo->createNewTemplate('T', 'K', 'A', 'B');
    $this->repo->save($template);

    $this->repo->saveCompiled($template, "<?php /* compiled template */ ?>");
    Assert::assertEquals("<?php /* compiled template */ ?>", $this->ctp->retrieve($template->templateId)['body']);
  }

  public function testAddCompiledTemplateForNonExistingTemplate(): void {
    $this->expectException(RecordNotFoundException::class);
    $template = $this->repo->createNewTemplate('T', 'K', 'A', 'B');
    $this->repo->saveCompiled($template, 'body');
  }

  public function testGetNewestTemplateByKey(): void {
    $key = __FUNCTION__;
    $this->p->persist($this->buildTemplate($key,'B','2000-01-02'));
    $this->p->persist($this->buildTemplate($key,'A','2000-01-01'));
    $this->p->persist($this->buildTemplate($key,'C','2000-01-03'));
    $template = $this->repo->getTemplateByKey($this->docType, $key);
    Assert::assertInstanceOf(Template::class, $template);
    Assert::assertEquals('C', $template->name);
  }

  public function testGetTemplateById(): void {
    $key = __FUNCTION__;
    $t = $this->fakeTemplateArray($this->docType, $key);
    $this->p->persist($this->fakeTemplateArray($this->docType));
    $this->p->persist($t);
    $this->p->persist($this->fakeTemplateArray($this->docType));
    $template = $this->repo->getTemplateById($t['templateId']);
    Assert::assertInstanceOf(Template::class, $template);
    Assert::assertEquals($t['templateId'], $template->templateId);
  }

  public function testGetCompiledTemplateById(): void {
    $t = $this->fakeTemplateArray();
    $ct = $this->fakeCompiledTemplate($t);
    $this->p->persist($t);
    $this->ctp->persist($ct);
    $template = $this->repo->getCompiledTemplateById($t['templateId']);
    Assert::assertInstanceOf(CompiledTemplate::class, $template);
    Assert::assertEquals($t['templateId'], $template->templateId);
  }

  public function testSave(): void {
    $templateArray = $this->fakeTemplateArray($this->docType, __FUNCTION__);
    $template = Template::fromState($templateArray);
    $this->repo->save($template);
    $fromDb = $this->repo->getTemplateById($template->templateId);
    Assert::assertEquals($templateArray, $fromDb->toArray());
  }

  public function testFindByDocType(): void {
    $t = $this->buildTemplate(__FUNCTION__, 'A', '2000-01-01');
    $t2 = $this->buildTemplate(__FUNCTION__, 'A', '1999-01-01');
    $this->p->persist($t);
    $this->p->persist($t2);
    $fromDb = $this->repo->findByDocType($this->docType);
    Assert::assertCount(1, $fromDb);
    Assert::assertContainsEquals(Template::fromState($t), $fromDb);
    Assert::assertNotContainsEquals(Template::fromState($t2), $fromDb);
  }

  public function test_LookupId_shouldThrowExceptionIfNoneFound(): void {
    $this->expectException(RecordNotFoundException::class);
    $this->repo->lookupId($this->docType, __FUNCTION__);
  }

  public function testLookupIdFromKey(): void {
    $p = $this->fakePersistence('template', 'templateId');
    $ctp = $this->fakePersistence('compiled_template', 'templateId');
    $p->persist([
        'docType' => 'dt',
        'templateId' => 'tid',
        'templateKey' => 'tk',
        'body' => 'Hi {{name}}'
    ]);
    $repo = new TemplateRepository($p, $ctp);
    Assert::assertEquals('tid', $repo->lookupId('dt', 'tk'));
  }

  public function testGetNewestTemplateIdWhenLookupByKey(): void {
    $key = __FUNCTION__;
    $a = $this->buildTemplate($key, 'A', '2000-01-01');
    $b = $this->buildTemplate($key, 'B', '2000-01-02');
    $c = $this->buildTemplate($key, 'C', '2000-01-03');
    $this->p->persist($b);
    $this->p->persist($a);
    $this->p->persist($c);
    $id = $this->repo->lookupId($this->docType, $key);
    Assert::assertEquals($c['templateId'], $id);
  }

  private function buildTemplate($key, $name, $createdAt): array {
    $t = $this->fakeTemplateArray($this->docType, $key);
    $t['createdAt'] = new DateTime($createdAt);
    $t['name'] = $name;
    return $t;
  }
}
