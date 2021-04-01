<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\Repository;


use HomeCEU\DTS\Db;
use HomeCEU\DTS\Entity\Partial;
use HomeCEU\DTS\Entity\PartialBuilder;
use HomeCEU\DTS\Persistence;
use HomeCEU\DTS\Repository\PartialRepository;
use HomeCEU\Tests\DTS\TestCase;

class PartialRepositoryTest extends TestCase {
  private Persistence $persistence;
  private PartialRepository $repo;

  protected function setUp(): void {
    parent::setUp();
    $this->persistence = $this->fakePersistence('partial', 'id');
    $this->repo = $repo = new PartialRepository($this->persistence);
  }

  public function testSavePartial(): void {
    $partial = $this->createSamplePartial();
    $this->repo->save($partial);
    $this->assertEquals($partial->toArray(), $this->persistence->retrieve($partial->id));
  }

  public function testGetPartialById(): void {
    $partial = $this->createSamplePartial();
    $this->persist($partial);
    $found = $this->repo->getById($partial->id);
    $this->assertEquals($partial, $found);
  }

  public function testFindPartialsByDocType(): void {
    $p1 = $this->createSamplePartial('DT');
    $p2 = $this->createSamplePartial('DT');
    $this->persist($p1, $p2);

    $partials = $this->repo->findByDocType('DT');
    $this->assertCount(2, $partials);
    foreach ($partials as $partial) {
      $this->assertInstanceOf(Partial::class, $partial);
    }
  }

  protected function createSamplePartial(string $docType = 'doc_type'): Partial {
    return PartialBuilder::create()
        ->withMetadata(['type' => 'standard'])
        ->withName('user_full_name')
        ->withDocType($docType)
        ->withAuthor('Test Author')
        ->withBody('body')
        ->build();
  }

  private function persist(Partial ...$rows): void {
    foreach ($rows as $row) {
      $this->persistence->persist($row->toArray());
    }
  }
}
