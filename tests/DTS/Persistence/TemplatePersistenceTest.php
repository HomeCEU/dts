<?php


namespace HomeCEU\Tests\DTS\Persistence;


use Exception;
use HomeCEU\DTS\Db;
use HomeCEU\DTS\Entity\Template;
use HomeCEU\DTS\Persistence\TemplatePersistence;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;

class TemplatePersistenceTest extends TestCase {

  /** @var TemplatePersistence */
  protected $p;

  protected $docType;

  protected $cleanupDocTypes = [

  ];

  /** @var Db\Connection */
  protected $db;

  protected function setUp(): void {
    parent::setUp();
    $this->db = Db::newConnection();
    $this->p = new TemplatePersistence($this->db);
    $this->docType = 'TemplatePersistenceTest-'.time();
    $this->addCleanupDoctype($this->docType);
  }

  protected function tearDown(): void {
    foreach ($this->cleanupDocTypes as $docType) {
      $this->db->deleteWhere('template', ['doc_type'=>$docType]);
    }
    parent::tearDown();
  }

  protected function addCleanupDoctype($doctype) {
    $this->cleanupDocTypes[] = $doctype;
  }

  protected function newPersistedTemplate(array $overwrite): Template {
    if (empty($overwrite['docType'])) {
      $overwrite['docType'] = $this->docType;
    }

    $t = $this->newTemplate($overwrite);
    $this->p->persist($t->toArray());
    if (!in_array($t->docType, $this->cleanupDocTypes)) {
      $this->addCleanupDoctype($t->docType);
    }
    return $t;
  }

  public function testFilterBySearchString() {
    $this->newPersistedTemplate([
        'docType' => $this->uniqueName($this->docType, 'nil'),
        'templateKey' => $this->uniqueName(__FUNCTION__, 'nil'),
        'name' => $this->uniqueName(self::faker()->name, 'nil'),
        'author' => $this->uniqueName(self::faker()->name, 'nil')
    ]);
    $t = $this->newPersistedTemplate([
        'docType' => $this->uniqueName($this->docType, 'foo'),
        'templateKey' => $this->uniqueName(__FUNCTION__, 'bar'),
        'name' => $this->uniqueName(self::faker()->name, 'baz'),
        'author' => $this->uniqueName(self::faker()->name, 'fin')
    ]);

    $results = $this->p->filterBySearchString('foo bar baz fin');

    $this->assertSearchMatches($results, $t);
  }

  public function testFilterBySearchStringWithMultipleResults() {
    $this->newPersistedTemplate([
        'docType' => $this->uniqueName($this->docType, 'nil'),
        'templateKey' => $this->uniqueName(__FUNCTION__, 'nil'),
        'name' => $this->uniqueName(self::faker()->name, 'nil'),
        'author' => $this->uniqueName(self::faker()->name, 'nil')
    ]);
    $this->newPersistedTemplate([
        'docType' => $this->uniqueName($this->docType, 'foo'),
        'templateKey' => $this->uniqueName(__FUNCTION__, 'bar'),
        'name' => $this->uniqueName(self::faker()->name, 'baz'),
        'author' => $this->uniqueName(self::faker()->name, 'fin')
    ]);
    $this->newPersistedTemplate([
        'docType' => $this->uniqueName($this->docType, 'foo'),
        'templateKey' => $this->uniqueName(__FUNCTION__, 'bar'),
        'name' => $this->uniqueName(self::faker()->name, 'baz'),
        'author' => $this->uniqueName(self::faker()->name, 'fin')
    ]);
    $results = $this->p->filterBySearchString('foo bar baz fin');
    Assert::assertCount(2, $results);
  }

  public function testListDocTypes() {
    $dtA = uniqid('A_');
    $dtB = uniqid('B_');
    $dtC = uniqid('C_');

    // Load Fixture Data
    $fixtureData = [
        [ 'docType' => $dtA, 'templateKey' => 'k1' ], // 3 versions of k1
        [ 'docType' => $dtA, 'templateKey' => 'k1' ],
        [ 'docType' => $dtA, 'templateKey' => 'k1' ],
        [ 'docType' => $dtA ], // will be unique key
        [ 'docType' => $dtA ], // so should be 3 A's
        [ 'docType' => $dtB],
        [ 'docType' => $dtB], // 2 B's
        [ 'docType' => $dtC], // 1 C
    ];
    $expectedDoctypeCounts = [
        $dtA => 3,
        $dtB => 2,
        $dtC => 1
    ];
    $this->loadTemplateFixtures($fixtureData);

    $foundTypes = $this->p->listDocTypes();
    $foundTypeConstants = array_column($foundTypes, 'docType');

    foreach ($foundTypes as $foundType) {
      Assert::assertArrayHasKey('docType', $foundType);
      Assert::assertArrayHasKey('templateCount', $foundType);
      if (in_array($foundType, $foundTypeConstants)) {
        Assert::assertEquals($expectedDoctypeCounts[$foundType], $foundType['templateCount']);
      }
    }
  }

  protected function assertSearchMatches($results, Template $t) {
    Assert::assertCount(1, $results);
    Assert::assertArrayHasKey('templateId', $results[0]);
    Assert::assertEquals($t->templateId, $results[0]['templateId']);
  }

  public function testGenerateId() {
    $id1 = $this->p->generateId();
    $id2 = $this->p->generateId();
    Assert::assertNotEmpty($id1);
    Assert::assertNotEmpty($id2);
    Assert::assertNotEquals($id1, $id2);
  }

  public function testCanRetrievePersistedRecord() {
    $record = $this->fakeTemplateArray($this->docType);
    $this->p->persist($record);
    $retrieved = $this->p->retrieve($record['templateId']);
    Assert::assertEquals($record, $retrieved);
  }

  public function testNoDelete() {
    $record = $this->fakeTemplateArray($this->docType);
    $this->p->persist($record);
    $this->expectException(Exception::class);
    $this->p->delete($record['templateId']);
  }

  /**
   * @param string $k
   * @param array $gatheredTypes
   * @return bool
   */
  private function hasType(string $k, array $gatheredTypes): bool {
    return in_array($k, $gatheredTypes);
  }

  private function loadTemplateFixtures(array $fixtureData): void {
    foreach ($fixtureData as $data) {
      $this->newPersistedTemplate($data);
    }
  }
}
