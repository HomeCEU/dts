<?php


namespace HomeCEU\Tests\DTS\Repository;

use HomeCEU\DTS\Entity\DocData;
use HomeCEU\DTS\Persistence;
use HomeCEU\DTS\Persistence\InMemory\DocDataPersistence;
use HomeCEU\DTS\Repository\DocDataRepository;
use HomeCEU\Tests\Faker;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;

class DocDataRepositoryTest extends TestCase {
  const ENTITY_TYPE = 'person';

  protected Persistence $persistence;
  protected DocDataRepository $repo;

  protected function setUp(): void {
    parent::setUp();
    $this->persistence = $this->persistence();
    $this->repo = new DocDataRepository($this->persistence);
  }

  public function testNewEntity() {
    $fake = Faker::generator();
    $type = self::ENTITY_TYPE;
    $key = $fake->md5;
    $data = $this->profileData();
    $e = $this->repo->newDocData($type, $key, $data);
    $this->assertSame($type, $e->docType);
    $this->assertSame($key, $e->dataKey);
    $this->assertSame($data, $e->data);
    $this->assertNotEmpty($e->dataId);;
    $this->assertNotEmpty($e->createdAt);
  }


  public function testSave() {
    $fake = Faker::generator();
    $type = self::ENTITY_TYPE;
    $key = $fake->md5;
    $data = $this->profileData();
    $entity = $this->repo->newDocData($type, $key, $data);
    $this->repo->save($entity);
    $savedEntity = $this->persistence->retrieve($entity->dataId);
    $this->assertEquals($entity->toArray(), $savedEntity);
  }

  public function testGetDocDataById() {
    $d = $this->fakeDocDataArray(__FUNCTION__);
    $this->persistence->persist($this->fakeDocDataArray());
    $this->persistence->persist($d);
    $this->persistence->persist($this->fakeDocDataArray());

    $docData = $this->repo->getByDocDataId($d['dataId']);
    Assert::assertInstanceOf(DocData::class, $docData);
    Assert::assertEquals($d['dataId'], $docData->dataId);
  }

  public function testDocDataHistory() {
    $type = 'dt1';
    $key = 'key';

    $d = $this->fakeDocDataArray();
    $d2 = $this->fakeDocDataArray();
    $d['docType'] = $type;
    $d['dataKey'] = $key;
    $d2['docType'] = $type;
    $d2['dataKey'] = $key;
    $this->persistence->persist($d);
    $this->persistence->persist($d2);
    $this->assertAllVersions($type, $key);
  }

  public function testLookupIdFromKey() {
    $p = $this->fakePersistence('docdata', 'dataId');
    $p->persist([
        'docType' => 'dt',
        'dataId' => 'did',
        'dataKey' => 'dk',
        'data'=>['name'=>'Fred']
    ]);
    $repo = new DocDataRepository($p);
    Assert::assertEquals('did', $repo->lookupId('dt','dk'));
  }

  protected function fakeDocData($key=null) {
    if (is_null($key)) $key = uniqid();
    $type = self::ENTITY_TYPE;
    $data = ['hash'=>Faker::generator()->md5];
    return $this->repo->newDocData($type, $key, $data);
  }

  protected function fakeDocDataArray($key=null) {
    return $this->fakeDocData($key)->toArray();
  }

  protected function persistence() {
    return new DocDataPersistence();
  }

  protected function profileData() {
    $fake = Faker::generator();
    return [
        "firstName" => $fake->firstName,
        "lastName"  => $fake->lastName,
        "address"   => $fake->address,
        "email"     => $fake->email
    ];
  }

  private function assertAllVersions(string $docType, string $dataKey): void {
    $this->assertCount(2, $this->repo->allVersions($docType, $dataKey));
  }
}
