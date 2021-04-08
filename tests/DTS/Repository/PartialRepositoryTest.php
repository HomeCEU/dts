<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\Repository;


use HomeCEU\DTS\Persistence;
use HomeCEU\DTS\Render\PartialInterface;
use HomeCEU\DTS\Repository\PartialRepository;
use HomeCEU\Tests\DTS\TestCase;
use HomeCEU\Tests\DTS\PartialTestTrait;

class PartialRepositoryTest extends TestCase {
  use PartialTestTrait;

  private Persistence $persistence;
  private PartialRepository $repo;

  protected function setUp(): void {
    parent::setUp();
    $this->persistence = $this->fakePersistence('partial', 'id');
    $this->repo = $repo = new PartialRepository($this->persistence);
  }

  public function testSavePartial(): void {
    $partial = $this->createSamplePartial('', '');
    $this->repo->save($partial);
    $this->assertEquals($partial->toArray(), $this->persistence->retrieve($partial->get('id')));
  }

  public function testGetPartialById(): void {
    $partial = $this->createSamplePartial('DT', 'a_partial');
    $this->persist($partial);
    $found = $this->repo->getById($partial->get('id'));
    $this->assertEquals($partial, $found);
  }

  public function testFindPartialsByDocType(): void {
    $p1 = $this->createSamplePartial('DT', 'a_partial');
    $p2 = $this->createSamplePartial('DT', 'a_partial');
    $this->persist($p1, $p2);

    $partials = $this->repo->findByDocType('DT');
    $this->assertCount(1, $partials);
    $this->assertEquals($p2->get('id'), $partials[0]->id);
  }

  private function persist(PartialInterface ...$rows): void {
    foreach ($rows as $row) {
      $this->persistence->persist($row->toArray());
    }
  }
}
