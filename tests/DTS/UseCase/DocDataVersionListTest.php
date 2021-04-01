<?php


namespace HomeCEU\Tests\DTS\UseCase;


use HomeCEU\DTS\Persistence\InMemory\DocDataPersistence;
use HomeCEU\DTS\Repository\DocDataRepository;
use HomeCEU\DTS\UseCase\DocDataVersionList;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;
use Ramsey\Uuid\Uuid;

class DocDataVersionListTest extends TestCase {
  /** @var DocDataPersistence */
  private $persistence;

  /** @var DocDataVersionList */
  private $usecase;

  protected function setUp(): void {
    parent::setUp();
    $this->persistence = new DocDataPersistence();
    $repo = new DocDataRepository($this->persistence);
    $this->usecase = new DocDataVersionList($repo);
  }

  public function testGetDocDataVersionsByDocTypeAndDataKey() {
    $docType = 'dataVersionListTest';
    $dataKey = 'A';
    for ($i=1; $i<=3; $i++) {
      $this->persistence->persist([
          'dataId' => Uuid::uuid1()->toString(),
          'docType' => $docType,
          'dataKey' => $dataKey
      ]);
    }
    $versions = $this->usecase->versions($docType, $dataKey);
    Assert::assertCount(3, $versions);
  }
}
