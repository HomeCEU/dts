<?php


namespace HomeCEU\Tests\DTS\UseCase;


use HomeCEU\DTS\Persistence\InMemory\DocDataPersistence;
use HomeCEU\DTS\Repository\DocDataRepository;
use HomeCEU\DTS\UseCase\DocDataVersionList;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;

class DocDataVersionListTest extends TestCase {
  /** @var DocDataPersistence */
  private $persistence;

  /** @var DocDataRepository */
  private $repo;

  /** @var DocDataVersionList */
  private $usecase;

  public function setUp(): void {
    parent::setUp();
    $this->persistence = new DocDataPersistence();
    $this->repo = new DocDataRepository($this->persistence);
    $this->usecase = new DocDataVersionList($this->repo);
  }

  public function testGetDocDataVersionsByDocTypeAndDataKey() {
    $docType = 'dataVersionListTest';
    $dataKey = 'A';
    for ($i=1; $i<=3; $i++) {
      $this->persistence->persist([
          'dataId' => $i,
          'docType' => $docType,
          'dataKey' => $dataKey
      ]);
    }
    $versions = $this->usecase->versions($docType, $dataKey);
    Assert::assertCount(3, $versions);
  }
}