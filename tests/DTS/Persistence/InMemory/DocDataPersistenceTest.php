<?php


namespace HomeCEU\Tests\DTS\Persistence\InMemory;

use HomeCEU\DTS\Repository\RecordNotFoundException;
use HomeCEU\Tests\DTS\TestCase;
use HomeCEU\DTS\Persistence\InMemory\DocDataPersistence;
use PHPUnit\Framework\Assert;

class DocDataPersistenceTest extends TestCase {

  /** @var  DocDataPersistence */
  private $sut;
  
  private $idCol = 'dataId';

  public function testGetTable() {
    $this->assertSame(
        'docdata',
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
    $this->expectException(RecordNotFoundException::class);
    $this->persistence()->retrieve($data[$this->idCol]);
  }

  public function testDeleteIdThatDoesNotExistThrowsException() {
    $this->expectException(RecordNotFoundException::class);
    $this->persistence()->delete(99);
  }

  public function testGenerateId() {
    $this->assertNotEmpty($this->persistence()->generateId());
  }

  public function testFind() {
    $p = $this->persistence();
    $p->persist(['dataId'=>'a','lname'=>'smith']);
    $p->persist(['dataId'=>'b','lname'=>'doe']);
    $p->persist(['dataId'=>'c','lname'=>'smith']);
    $results = $p->find(['lname'=>'smith']);
    $expected = [
        ['dataId'=>'a','lname'=>'smith'],
        ['dataId'=>'c','lname'=>'smith']
    ];
    Assert::assertEquals($expected, $results);
  }


  protected function persistence(): DocDataPersistence {
    return $this->sut ?: $this->sut= new DocDataPersistence();
  }

}
