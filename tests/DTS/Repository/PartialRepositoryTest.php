<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\Repository;


use HomeCEU\DTS\Entity\Partial;
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

  public function testCreatePartial(): void {
    $partial = $this->createSamplePartial();
    $this->assertInstanceOf(Partial::class, $partial);
    $this->assertNotEmpty($partial->id);
    $this->assertNotEmpty($partial->createdAt);
  }

  public function testSavePartial(): void {
    $partial = $this->createSamplePartial();
    $savedId = $this->repo->save($partial);
    $this->assertEquals($partial->toArray(), $this->persistence->retrieve($savedId));
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
    return $this->repo->create(
        $docType,
        'user_full_name',
        'Test Author',
        '{{ user.first_name }} {{ user.lastname }}'
    );
  }

  private function persist(Partial ...$rows): void {
    foreach ($rows as $row) {
      $this->persistence->persist($row->toArray());
    }
  }
}
