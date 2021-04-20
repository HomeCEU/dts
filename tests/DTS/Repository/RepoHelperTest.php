<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\Repository;


use HomeCEU\DTS\Persistence;
use HomeCEU\DTS\Repository\RepoHelper;
use HomeCEU\Tests\DTS\TestCase;

class RepoHelperTest extends TestCase {
  private Persistence $persistence;
  private RepoHelper $helper;

  protected function setUp(): void {
    parent::setUp();
    $this->persistence = $this->fakePersistence('template', 'id');
    $this->helper = new RepoHelper($this->persistence);
  }

  /**
   * This function extracts unique values from a specific array column.
   * Sometimes the resulting array has missing indexes. e.g. [0 => 'something', 2 => 'something else']
   * We actually want the array to look like this: [0 => 'something', 1 => 'something else']
   * This test ensures that happens.
   */
  public function testExtractUniquePropertyCreatesStandardIndexArray(): void {
    $this->persist(
        ['id' => uniqid(), 'type' => 'TYPE', 'key' => '1234'],
        ['id' => uniqid(), 'type' => 'TYPE', 'key' => '1234'],
        ['id' => uniqid(), 'type' => 'TYPE', 'key' => 'abc'],
        ['id' => uniqid(), 'type' => 'TYPE', 'key' => 'abc'],
    );
    $values = $this->helper->extractUniqueProperty($this->persistence->find(['type' => 'TYPE']), 'key');
    $this->assertCount(2, $values);
    $this->assertArrayHasKey(0, $values);
    $this->assertArrayHasKey(1, $values);
  }

  private function persist(...$rows): void {
    foreach ($rows as $row) {
      $this->persistence->persist($row);
    }
  }
}
