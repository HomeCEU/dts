<?php declare(strict_types=1);


namespace DTS\Persistence;


use HomeCEU\DTS\Db;
use HomeCEU\DTS\Persistence\CompiledTemplatePersistence;
use HomeCEU\DTS\Persistence\TemplatePersistence;
use HomeCEU\DTS\Repository\RecordNotFoundException;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;
use Ramsey\Uuid\Uuid;

class CompiledTemplatePersistenceTest extends TestCase {
  protected $persistence;
  protected $db;
  protected $template;

  protected function setUp(): void {
    parent::setUp();
    $this->db = Db::newConnection();
    $this->db->beginTransaction();
    $this->persistence = new CompiledTemplatePersistence($this->db);
    $this->template = $this->fakeTemplateArray();
    (new TemplatePersistence($this->db))->persist($this->template);
  }

  protected function tearDown(): void {
    $this->db->rollBack();
    parent::tearDown();
  }

  public function testCanRetrievePersistedRecord(): void {
    $cTemplate = $this->fakeCompiledTemplate($this->template);
    $this->persistence->persist($cTemplate);

    $retrieved = $this->persistence->retrieve($cTemplate['templateId']);
    Assert::assertEquals($cTemplate, $retrieved);
  }

  public function testCanFindRecordByTemplateDocTypeAndKey(): void
  {
    $cTemplate = $this->fakeCompiledTemplate($this->template);
    $this->persistence->persist($cTemplate);

    $retrieved = $this->persistence->findBy(
        $this->template['docType'],
        $this->template['templateKey']
    );
    Assert::assertEquals($cTemplate, $retrieved);
  }

  public function testRecordNotFound(): void {
    $this->expectException(RecordNotFoundException::class);
    $this->expectExceptionMessage('compiled template');
    $this->persistence->retrieve(Uuid::uuid4()->toString());
  }

  public function testFindByTemplateDocTypeAndKeyRecordNotFound(): void
  {
    Assert::assertNull($this->persistence->findBy('made-up-type', 'made-up-key'));
  }
}
