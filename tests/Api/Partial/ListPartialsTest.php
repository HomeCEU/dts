<?php declare(strict_types=1);


namespace HomeCEU\Tests\Api\Partial;


use HomeCEU\DTS\Persistence;
use HomeCEU\DTS\Persistence\PartialPersistence;
use HomeCEU\Tests\Api\ApiTestCase;

class ListPartialsTest extends ApiTestCase {
  private Persistence $persistence;

  protected function setUp(): void {
    parent::setUp();
    $this->persistence = new PartialPersistence($this->db);
  }

  public function testListPartialsDocTypeNotProvided(): void {
    $response = $this->get('/api/v1/partial');
    $this->assertStatus(400, $response);
  }

  public function testNoPartialsFoundForDoctype(): void {
    $response = $this->get('/api/v1/partial?docType=DT');
    $this->assertStatus(200, $response);

    $content = $this->getResponseJsonAsObj($response);
    $this->assertEquals(0, (int) $content->total);
    $this->assertCount(0, $content->items);
  }

  public function testListPartials(): void {
    $this->persistence->persist([
        'id' => uniqid(),
        'docType' => 'DT',
        'body' => '{{ name }}',
        'name' => 'a_partial',
        'createdAt' => new \DateTime(),
        'author' => 'Test Author'
    ]);
    $response = $this->get('/api/v1/partial?docType=DT');
    $this->assertStatus(200, $response);

    $content = $this->getResponseJsonAsObj($response);
    $this->assertEquals(1, (int) $content->total);
    $this->assertCount(1, $content->items);
  }
}
