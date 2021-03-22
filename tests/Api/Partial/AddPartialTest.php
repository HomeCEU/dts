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
        'author' => 'Test Author'
    ];
    $response = $this->post('/api/v1/partial', $request);
    $this->assertPartialWasCreated($response);
    $this->assertResponseHasCorrectJsonSchema($response, $this->expectedKeys());
  }

  public function testInvalidRequests(): void {
    $this->markTestIncomplete('Not implemented');
  }

  private function expectedKeys(): array {
    return ['id', 'docType', 'author', 'createdAt', 'bodyUri', 'name'];
  }

  private function assertPartialWasCreated(ResponseInterface $response): void {
    $this->assertStatus(201, $response);

    $content = $this->getResponseJsonAsArray($response);
    $this->assertNotEmpty($this->persistence->retrieve($content['id']));
  }

  protected function assertResponseHasCorrectJsonSchema(ResponseInterface $response, array $expectedKeys): void {
    $content = $this->getResponseJsonAsArray($response);

    $this->assertContentType('application/json', $response);
    foreach ($expectedKeys as $key) {
      $this->assertArrayHasKey($key, $content);
    }
    foreach ($content as $k => $v) {
      $this->assertContains($k, $expectedKeys, "Response Returned a Key that it shouldn't have");
    }
    $this->assertEquals("/api/v1/partial/{$content['id']}", $content['bodyUri']);
  }
}
