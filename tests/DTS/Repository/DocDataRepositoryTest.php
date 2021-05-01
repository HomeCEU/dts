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
    $this->assertSame($key, $e->key);
    $this->assertSame($data, $e->data);
    $this->assertNotEmpty($e->id);;
    $this->assertNotEmpty($e->createdAt);
  }


  public function testSave() {
    $fake = Faker::generator();
    $type = self::ENTITY_TYPE;
    $key = $fake->md5;
    $data = $this->profileData();
    $entity = $this->repo->newDocData($type, $key, $data);
    $this->repo->save($entity);
    $savedEntity = $this->persistence->retrieve($entity->id);
    $this->assertEquals($entity->toArray(), $savedEntity);
  }

  public function testGetDocDataById() {
    $d = $this->fakeDocDataArray(__FUNCTION__);
    $this->persistence->persist($this->fakeDocDataArray());
    $this->persistence->persist($d);
    $this->persistence->persist($this->fakeDocDataArray());

    $docData = $this->repo->getByDocDataId($d['id']);
    Assert::assertInstanceOf(DocData::class, $docData);
    Assert::assertEquals($d['id'], $docData->id);
  }

  public function testDocDataHistory() {
    $type = 'dt1';
    $key = 'key';

    $d = $this->fakeDocDataArray();
    $d2 = $this->fakeDocDataArray();
    $d['docType'] = $type;
    $d['key'] = $key;
    $d2['docType'] = $type;
    $d2['key'] = $key;
    $this->persistence->persist($d);
    $this->persistence->persist($d2);
    $this->assertAllVersions($type, $key);
  }

  public function testLookupIdFromKey() {
    $p = $this->fakePersistence('docdata', 'id');
    $p->persist([
        'docType' => 'dt',
        'id' => 'did',
        'key' => 'dk',
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

  private function assertAllVersions(string $docType, string $key): void {
    $this->assertCount(2, $this->repo->allVersions($docType, $key));
  }
}
