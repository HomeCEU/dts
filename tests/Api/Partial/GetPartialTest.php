<?php declare(strict_types=1);


namespace Api\Partial;


use HomeCEU\DTS\Entity\Partial;
use HomeCEU\DTS\Entity\PartialBuilder;
use HomeCEU\DTS\Persistence;
use HomeCEU\DTS\Persistence\PartialPersistence;
use HomeCEU\Tests\Api\ApiTestCase;

class GetPartialTest extends ApiTestCase {
  private Persistence $persistence;

  protected function setUp(): void {
    parent::setUp();
    $this->persistence = new PartialPersistence($this->di->get('dbConnection'));
  }

  public function testGetPartialNotFound(): void {
    $this->assertStatus(404, $this->get("/api/v1/partial/" . uniqid()));
  }

  public function testGetPartialById(): void {
    $partial = $this->createPartial();
    $this->persistence->persist($partial->toArray());
    $response = $this->get("/api/v1/partial/{$partial->id}");
    $this->assertStatus(200, $response);
    $this->assertEquals($partial->body, (string) $response->getBody());
  }

  private function createPartial(): Partial {
    return PartialBuilder::create()
        ->withBody('{{ student.name }}')
        ->withDocType('DT')
        ->withName('test_partial')
        ->withAuthor('Dan')
        ->withMetadata([])
        ->build();
  }
}
