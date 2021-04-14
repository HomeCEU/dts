<?php


namespace HomeCEU\Tests\DTS\Persistence;


use HomeCEU\DTS\Db;
use HomeCEU\DTS\Entity\DocData;
use HomeCEU\DTS\Persistence\DocDataPersistence;
use HomeCEU\Tests\DTS\TestCase;
use HomeCEU\Tests\Faker;
use PHPUnit\Framework\Assert;

class DocDataPersistenceTest extends TestCase {

  /** @var  DocDataPersistence */
  private $p;

  /** @var Db\Connection */
  private $db;

  private $cleanupCalls = [];

  protected function setUp(): void {
    parent::setUp();
    $this->db = Db::newConnection();
    $this->p = new DocDataPersistence($this->db);
  }

  protected function tearDown(): void {
    parent::tearDown();
    foreach ($this->cleanupCalls as $func) {
      call_user_func($func);
    }
  }

  public function testGenerateId() {
    $id1 = $this->p->generateId();
    $id2 = $this->p->generateId();
    Assert::assertNotEmpty($id1);
    Assert::assertNotEquals($id1, $id2);
  }

  public function testPersist() {
    $data = $this->docData();
    $this->persist($data);
    Assert::assertEquals(
        1,
        $this->db->count(
            DocDataPersistence::TABLE,
            'data_id=:data_id',
            [':data_id'=>$data->dataId]
        )
    );
  }

  public function testCanRetrieveSavedRecord() {
    $data = $this->docData();
    $this->persist($data);
    $retrieved = $this->p->retrieve($data->dataId);
    Assert::assertEquals($data->toArray(), $retrieved);
  }

  public function testCanSpecifyWhichColsToRetrieve() {
    $data = $this->docData();
    $this->persist($data);
    $cols = ['dataId','dataKey'];
    $retrieved = $this->p->retrieve($data->dataId, $cols);
    $expected = [
        'dataId' => $data->dataId,
        'dataKey' => $data->dataKey
    ];
    Assert::assertEquals($expected, $retrieved);
  }

  public function testFind() {
    $a1 = $this->docData('a');
    $b1 = $this->docData('b');
    $a2 = $this->docData('a');
    $this->persist($a1);
    $this->persist($b1);
    $this->persist($a2);
    $expectedIds = [$a1->dataId, $a2->dataId];
    $results = $this->p->find(['dataKey'=>'a']);
    Assert::assertCount(2, $results);
    Assert::assertNotEquals($results[0], $results[1]);
    foreach ($results as $row) {
      Assert::assertContains($row['dataId'], $expectedIds);
    }
  }

  public function testFindWithSpecificCols() {
    $cols = ['docType', 'dataKey'];
    $this->persist($this->docData('a'));
    $this->persist($this->docData('b'));
    $results = $this->p->find(['dataKey'=>'a'], $cols);
    $row = $results[0];
    Assert::assertCount(2, $row);
    Assert::assertContains('docType', array_keys($row));
    Assert::assertContains('dataKey', array_keys($row));
  }

  public function testNoDelete() {
    $data = $this->docData();
    $this->persist($data);
    $this->expectException(\Exception::class);
    $this->p->delete($data->dataId);
  }

  protected function persist(DocData $data) {
    $array = $data->toArray();
    $this->p->persist($array);
    $this->addCleanup(function() use($data){
      $table = DocDataPersistence::TABLE;
      $this->db->query("DELETE FROM {$table} WHERE data_id=?", $data->dataId);
    });
  }

  protected function addCleanup(callable $func) {
    array_push($this->cleanupCalls, $func);
  }

  protected function docData($dataKey=null) {
    $entityState = $this->fakeEntity($dataKey);
    return DocData::fromState($entityState);
  }

  protected function fakeEntity($dataKey=null) {
    $fake = Faker::generator();
    $key = $dataKey?:$fake->md5;
    return [
        'dataId'   => $fake->uuid,
        'docType' => 'courseCompletionCertificate',
        'dataKey'  => $key,
        'createdAt'  => $fake->iso8601,
        'data'       => [
            "firstName" => $fake->firstName,
            "lastName"  => $fake->lastName,
            "address"   => $fake->address,
            "email"     => $fake->email
        ]
    ];
  }
}
