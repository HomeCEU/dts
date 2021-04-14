<?php declare(strict_types=1);


namespace HomeCEU\Tests\Api\Partial;


use HomeCEU\DTS\Persistence;
use HomeCEU\Tests\Api\ApiTestCase;
use Psr\Http\Message\ResponseInterface;

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
        'author' => 'Test Author',
        'metadata' => ['type' => 'standard']
    ];
    $response = $this->post('/api/v1/partial', $request);
    $this->assertPartialWasCreated($response);
    $this->deletePartial($response);
  }

  public function testInvalidRequests(): void {
    $response = $this->post('/api/v1/partial', []);
    $this->assertStatus(400, $response);
  }

  private function assertResponseIsJson(ResponseInterface $response): void {
    $this->assertContentType('application/json', $response);
    $this->assertIsArray($this->getResponseJsonAsArray($response));
  }

  private function assertPartialWasCreated(ResponseInterface $response): void {
    $this->assertStatus(201, $response);
    $this->assertResponseIsJson($response);

    $content = $this->getResponseJsonAsArray($response);
    $partialArray = $this->persistence->retrieve($content['id']);
    $this->assertNotEmpty($partialArray['metadata']);
  }

  private function deletePartial(ResponseInterface $response) {
    $content = $this->getResponseJsonAsArray($response);
    $this->persistence->delete($content['id']);
  }
}
