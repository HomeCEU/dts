<?php


namespace HomeCEU\Tests\DTS\UseCase;


use DateTime;
use HomeCEU\DTS\Db;
use HomeCEU\DTS\Persistence\DocDataPersistence;
use HomeCEU\DTS\Repository\DocDataRepository;
use HomeCEU\DTS\UseCase\GetDocData;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;

class GetDocDataTest extends TestCase {
  /** @var Db\Connection  */
  private $db;

  /** @var DocDataPersistence  */
  private $p;

  /** @var GetDocData */
  private $useCase;

  /** @var array[] */
  private $fixtureData;

  /** @var string */
  private $docType;

  protected function setUp(): void {
    parent::setUp();
    $this->db = Db::connection();
    $this->db->beginTransaction();
    $this->p = new DocDataPersistence($this->db);
    $repo = new DocDataRepository($this->p);
    $this->useCase = new GetDocData($repo);
    $this->docType = uniqid('GetDocDataTest');
    $this->loadFixtureData();
  }

  protected function tearDown(): void {
    parent::tearDown();
    $this->db->rollBack();
  }

  public function testGetByTypeAndKey() {
    // should return only the most recent version
    $expectedId = $this->fixtureData['A2']['id'];
    $docData = $this->useCase->getLatestVersion($this->docType, 'A');
    Assert::assertEquals($expectedId, $docData->id);
  }

  public function testGetById() {
    $id = $this->fixtureData['find']['id'];
    $docData = $this->useCase->getById($id);
    Assert::assertEquals($id, $docData->id);
    Assert::assertEquals($this->fixtureData['find'], $docData->toArray());
  }

  protected function loadFixtureData() {
    $day = 0;
    $this->fixtureData = [
        'A1' => [
            'id' => self::faker()->uuid,
            'docType' => $this->docType,
            'key' => 'A',
            'createdAt' => new DateTime('2020-01-0'.++$day)
        ],
        'A2' => [
            'id' => self::faker()->uuid,
            'docType' => $this->docType,
            'key' => 'A',
            'createdAt' => new DateTime('2020-01-0'.++$day)
        ],
        'B1' => [
            'id' => self::faker()->uuid,
            'docType' => $this->docType,
            'key' => 'B',
            'createdAt' => new DateTime('2020-01-0'.++$day)
        ],
        'B2' => [
            'id' => self::faker()->uuid,
            'docType' => $this->docType,
            'key' => 'B',
            'createdAt' => new DateTime('2020-01-0'.++$day)
        ],
        'find' => $this->docDataArray([
            'id' => self::faker()->uuid,
            'docType' => $this->docType
        ])
    ];
    foreach ($this->fixtureData as $r) {
      $this->p->persist($this->docDataArray($r));
    }
  }
}
