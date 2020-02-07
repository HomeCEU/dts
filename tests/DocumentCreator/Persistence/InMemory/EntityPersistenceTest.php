<?php


namespace HomeCEU\Tests\DocumentCreator\Persistence\InMemory;

use HomeCEU\Tests\DocumentCreator\TestCase;
use HomeCEU\DocumentCreator\Persistence\InMemory\EntityPersistence;

class EntityPersistenceTest extends TestCase {

  /** @var  EntityPersistence */
  private $sut;
  
  private $idCol = 'entityId';

  public function testGetTable() {
    $this->assertSame(
        'entity',
        $this->persistence()->getTable());
  }

  public function testIdCols() {
    $this->assertSame(
        [$this->idCol],
        $this->persistence()->idColumns()
    );
  }

  public function testInsert() {
    $data = [
        $this->idCol => $this->persistence()->generateId(),
        'username' => 'fred'
    ];
    $this->persistence()->persist($data);
    $this->assertSame(
        $data,
        $this->persistence()->retrieve($data[$this->idCol])
    );
  }

  public function testUpdate() {
    $data = [
        $this->idCol => $this->persistence()->generateId(),
        'username' => 'fred'
    ];
    $this->persistence()->persist($data);
    $data['username'] = 'john';
    $this->persistence()->persist($data);
    $this->assertSame(
        $data,
        $this->persistence()->retrieve($data[$this->idCol])
    );
  }

  public function testDelete() {
    $data = [
        $this->idCol => $this->persistence()->generateId(),
        'username' => 'fred'
    ];
    $this->persistence()->persist($data);
    $this->persistence()->delete($data[$this->idCol]);
    $this->expectException(\OutOfBoundsException::class);
    $this->persistence()->retrieve($data[$this->idCol]);
  }

  public function testDeleteIdThatDoesNotExistThrowsException() {
    $this->expectException(\OutOfBoundsException::class);
    $this->persistence()->delete(99);
  }

  public function testGenerateId() {
    $this->assertNotEmpty($this->persistence()->generateId());
  }

  protected function persistence(): EntityPersistence {
    return $this->sut ?: $this->sut= new EntityPersistence();
  }

}