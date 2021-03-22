<?php declare(strict_types=1);


namespace HomeCEU\Tests\Api\Partial;


use HomeCEU\DTS\Persistence;
use HomeCEU\Tests\Api\ApiTestCase;

class AddPartialTest extends ApiTestCase {
  private Persistence $persistence;

  protected function setUp(): void {
    parent::setUp();
    $this->persistence = new Persistence\PartialPersistence($this->di->get('dbConnection'));
  }

  public function testAddPartial(): void {
    $request = [
        'docType' => 'DT',
        'body' => '{{ user.first_name }} {{ user.last_name }}',
        'name' => 'user_full_name',
        'author' => 'Test Author'
    ];
    $response = $this->post('/api/v1/partial', $request);
    $contentObj = $this->getResponseJsonAsObj($response);

    $this->assertStatus(201, $response);
    $this->assertContentType('application/json', $response);
    $this->assertObjectHasAttribute('id', $contentObj);
    $this->assertEquals("/api/v1/partial/{$contentObj->id}", $contentObj->bodyUri);
    $this->assertPartialWasCreated($contentObj->id);
  }

  public function testInvalidRequests(): void {
    $this->markTestIncomplete('Not implemented');
  }

  private function assertPartialWasCreated(string $id): void {
    $this->assertNotEmpty($this->persistence->retrieve($id));
  }
}
