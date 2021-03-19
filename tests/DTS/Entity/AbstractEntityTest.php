<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\Entity;


use HomeCEU\DTS\AbstractEntity;
use PHPUnit\Framework\TestCase;

class AbstractEntityTest extends TestCase {
  public function testBuildFromState(): void {
      $state = ['p1' => 'test', 'p2' => 'test2'];
      $e = $this->abstract()::fromState($state);
      $this->assertInstanceOf(AbstractEntity::class, $e);
      $this->assertSetPublicProperties($state, $e);
  }

  public function testGetProperty(): void {
    $state = ['p1' => 'test', 'p2' => 'test2'];
    $e = $this->abstract()::fromState($state);
    $this->assertEquals($state['p1'], $e->get('p1'));
    $this->assertNull($e->get(uniqid()));
  }

  public function testConvertCreatedAtStringToDateTime(): void {
    $state = ['createdAt' => '2021-09-08'];
    $e = $this->abstract()::fromState($state);
    $this->assertInstanceOf(\DateTime::class, $e->get('createdAt'));
  }

  private function abstract(): AbstractEntity {
    return new class extends AbstractEntity {
      public $p1;
      public $p2;
      public $createdAt;
    };
  }

  private function assertSetPublicProperties(array $state, AbstractEntity $e) {
    foreach ($state as $k => $v) {
      $this->assertEquals($v, $e->$k);
    }
  }
}
