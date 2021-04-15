<?php declare(strict_types=1);


namespace HomeCEU\Tests\Api\Partial;


use HomeCEU\DTS\Persistence;
use HomeCEU\DTS\Persistence\PartialPersistence;
use HomeCEU\Tests\Api\ApiTestCase;
use Psr\Http\Message\ResponseInterface;

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
    $this->assertEquals(0, (int)$content->total);
    $this->assertCount(0, $content->items);
  }

  public function testListPartials(): void {
    $this->persistence->persist($this->samplePartialArray('DT', 'a_partial'));
    $response = $this->get('/api/v1/partial?docType=DT');
    $this->assertStatus(200, $response);
    $this->assertNumPartialsReturned(1, $response);
  }

  public function testListOnlyMostRecentPartialPerDocTypeAndNameCombo(): void {
    $p1 = $this->samplePartialArray('DT', 'a_partial');
    $p2 = $this->samplePartialArray('DT', 'a_partial');

    $this->persist($p1, $p2);

    $response = $this->get('/api/v1/partial?docType=DT');
    $this->assertStatus(200, $response);
    $this->assertNumPartialsReturned(1, $response);
  }

  public function samplePartialArray(string $docType, string $key): array {
    return [
        'id' => uniqid(),
        'docType' => $docType,
        'body' => '{{ name }}',
        'key' => $key,
        'createdAt' => new \DateTime(),
        'author' => 'Test Author',
        'metadata' => []
    ];
  }

  public function assertNumPartialsReturned(int $count, ResponseInterface $response): void {
    $content = $this->getResponseJsonAsObj($response);

    $this->assertEquals($count, (int)$content->total);
    $this->assertCount($count, $content->items);
  }

  private function persist(array ...$partials) {
    foreach ($partials as $partial) {
      $this->persistence->persist($partial);
    }
  }
}
