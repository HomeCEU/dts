<?php


namespace HomeCEU\Tests\DTS\Persistence;


use HomeCEU\DTS\Db\Config;
use HomeCEU\DTS\Db\Connection;
use HomeCEU\DTS\Persistence\AbstractPersistence;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;

class AbstractPersistenceTest extends TestCase {
  /** @var Connection */
  private $fakeDb;

  protected function setUp(): void {
    parent::setUp();
    $this->fakeDb = Connection::buildFromConfig(Config::sqlite());
  }

  public function hydrated() {
    return [
        "firstName" => "Fred",
        "birthDay" => new \DateTime("2000-01-01"),
        "location" => ["state" => "TX", "city" => "Dallas"]
    ];
  }

  public function mySqlForm() {
    return [
        "first_name" => "Fred",
        "birth_day" => "2000-01-01 00:00:00",
        "location" => '{"state":"TX","city":"Dallas"}'
    ];
  }

  public function keymap() {
    return [
        'firstName' => 'first_name',
        'lastName' => 'last_name',
        'birthDay'  => 'birth_day',
    ];
  }

  public function testFlatten() {
    $p = $this->persistence();
    $p->useKeyMap($this->keymap());
    $flattened = $p->flatten($this->hydrated());
    Assert::assertEquals($this->mySqlForm(), $flattened);
  }

  public function testHydrateFromMySQL() {
    $p = $this->persistence();
    $p->useKeyMap($this->keymap());
    $hydrated = $p->hydrate($this->mySqlForm());
    Assert::assertEquals($this->hydrated(), $hydrated);
  }

  protected function persistence() {
    return new class($this->fakeDb) extends AbstractPersistence {
      public function delete(string $id): void {}
    };
  }
}
