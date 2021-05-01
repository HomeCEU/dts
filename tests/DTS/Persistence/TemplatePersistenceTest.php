<?php


namespace HomeCEU\Tests\DTS\Persistence;


use Exception;
use HomeCEU\DTS\Db;
use HomeCEU\DTS\Entity\Template;
use HomeCEU\DTS\Persistence\TemplatePersistence;
use HomeCEU\DTS\Repository\RecordNotFoundException;
use HomeCEU\Tests\DTS\TestCase;

class TemplatePersistenceTest extends TestCase {
  protected Db\Connection $db;
  protected TemplatePersistence $p;
  protected string $docType;
  protected array $cleanupDocTypes = [];

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
        'key' => $this->uniqueName(__FUNCTION__, 'nil'),
        'name' => $this->uniqueName(self::faker()->name, 'nil'),
        'author' => $this->uniqueName(self::faker()->name, 'nil')
    ]);
    $t = $this->newPersistedTemplate([
        'docType' => $this->uniqueName($this->docType, 'foo'),
        'key' => $this->uniqueName(__FUNCTION__, 'bar'),
        'name' => $this->uniqueName(self::faker()->name, 'baz'),
        'author' => $this->uniqueName(self::faker()->name, 'fin')
    ]);

    $results = $this->p->filterBySearchString('foo bar baz fin');

    $this->assertSearchMatches($results, $t);
  }

  public function testFilterBySearchStringWithMultipleResults() {
    $this->newPersistedTemplate([
        'docType' => $this->uniqueName($this->docType, 'nil'),
        'key' => $this->uniqueName(__FUNCTION__, 'nil'),
        'name' => $this->uniqueName(self::faker()->name, 'nil'),
        'author' => $this->uniqueName(self::faker()->name, 'nil')
    ]);
    $this->newPersistedTemplate([
        'docType' => $this->uniqueName($this->docType, 'foo'),
        'key' => $this->uniqueName(__FUNCTION__, 'bar'),
        'name' => $this->uniqueName(self::faker()->name, 'baz'),
        'author' => $this->uniqueName(self::faker()->name, 'fin')
    ]);
    $this->newPersistedTemplate([
        'docType' => $this->uniqueName($this->docType, 'foo'),
        'key' => $this->uniqueName(__FUNCTION__, 'bar'),
        'name' => $this->uniqueName(self::faker()->name, 'baz'),
        'author' => $this->uniqueName(self::faker()->name, 'fin')
    ]);
    $results = $this->p->filterBySearchString('foo bar baz fin');
    $this->assertCount(2, $results);
  }

  public function testListDocTypes() {
    $dtA = uniqid('A_');
    $dtB = uniqid('B_');
    $dtC = uniqid('C_');

    // Load Fixture Data
    $fixtureData = [
        [ 'docType' => $dtA, 'key' => 'k1' ], // 3 versions of k1
        [ 'docType' => $dtA, 'key' => 'k1' ],
        [ 'docType' => $dtA, 'key' => 'k1' ],
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

    $foundDocTypeObjs = $this->p->listDocTypes();
    $docTypes = array_column($foundDocTypeObjs, 'docType');

    foreach ($docTypes as $k => $docType) {
      if (isset($expectedDoctypeCounts[$docType])) {
        $this->assertEquals($expectedDoctypeCounts[$docType], $foundDocTypeObjs[$k]['templateCount']);
      }
    }
  }

  protected function assertSearchMatches($results, Template $t) {
    $this->assertCount(1, $results);
    $this->assertArrayHasKey('id', $results[0]);
    $this->assertEquals($t->id, $results[0]['id']);
  }

  public function testGenerateId() {
    $id1 = $this->p->generateId();
    $id2 = $this->p->generateId();
    $this->assertNotEmpty($id1);
    $this->assertNotEmpty($id2);
    $this->assertNotEquals($id1, $id2);
  }

  public function testCanRetrievePersistedRecord() {
    $record = $this->fakeTemplateArray($this->docType);
    $this->p->persist($record);
    $retrieved = $this->p->retrieve($record['id']);
    $this->assertEquals($record, $retrieved);
  }

  public function testDelete() {
    $this->expectException(RecordNotFoundException::class);
    $record = $this->fakeTemplateArray($this->docType);
    $this->p->persist($record);
    $this->p->delete($record['id']);
    $this->p->retrieve($record['id']);
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
