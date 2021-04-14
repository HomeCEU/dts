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
    $expectedId = $this->fixtureData['A2']['dataId'];
    $docData = $this->useCase->getLatestVersion($this->docType, 'A');
    Assert::assertEquals($expectedId, $docData->dataId);
  }

  public function testGetById() {
    $dataId = $this->fixtureData['find']['dataId'];
    $docData = $this->useCase->getById($dataId);
    Assert::assertEquals($dataId, $docData->dataId);
    Assert::assertEquals($this->fixtureData['find'], $docData->toArray());
  }

  protected function loadFixtureData() {
    $day = 0;
    $this->fixtureData = [
        'A1' => [
            'dataId' => self::faker()->uuid,
            'docType' => $this->docType,
            'dataKey' => 'A',
            'createdAt' => new DateTime('2020-01-0'.++$day)
        ],
        'A2' => [
            'dataId' => self::faker()->uuid,
            'docType' => $this->docType,
            'dataKey' => 'A',
            'createdAt' => new DateTime('2020-01-0'.++$day)
        ],
        'B1' => [
            'dataId' => self::faker()->uuid,
            'docType' => $this->docType,
            'dataKey' => 'B',
            'createdAt' => new DateTime('2020-01-0'.++$day)
        ],
        'B2' => [
            'dataId' => self::faker()->uuid,
            'docType' => $this->docType,
            'dataKey' => 'B',
            'createdAt' => new DateTime('2020-01-0'.++$day)
        ],
        'find' => $this->docDataArray([
            'dataId' => self::faker()->uuid,
            'docType' => $this->docType
        ])
    ];
    foreach ($this->fixtureData as $r) {
      $this->p->persist($this->docDataArray($r));
    }
  }
}