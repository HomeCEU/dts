<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\Persistence;


use HomeCEU\DTS\Db;
use HomeCEU\DTS\Persistence\HotRenderPersistence;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;

class HotRenderPersistenceTest extends TestCase {
  private HotRenderPersistence $persistence;
  private Db\Connection $db;

  protected function setUp(): void {
    parent::setUp();
    $this->db = Db::newConnection();
    $this->db->beginTransaction();
    $this->persistence = new HotRenderPersistence($this->db);
  }

  protected function tearDown(): void {
    $this->db->rollBack();
    parent::tearDown();
  }

  public function testRetrievePersistedRecord(): void {
    $record = $this->fakeHotRenderRequestArray();
    $this->persistence->persist($record);
    Assert::assertEquals($record, $this->persistence->retrieve($record['id']));
  }

  public function testNoDelete() {
    $record = $this->fakeHotRenderRequestArray();
    $this->persistence->persist($record);
    $this->expectException(\Exception::class);
    $this->persistence->delete($record['id']);
  }

  protected function fakeHotRenderRequestArray(): array {
    return [
        'id' => $this->persistence->generateId(),
        'template' => '<?php /* a compiled template */ ?>',
        'data' => ['name' => 'test'],
        'createdAt' => new \DateTime('yesterday')
    ];
  }
}
