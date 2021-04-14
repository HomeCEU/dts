<?php declare(strict_types=1);


namespace HomeCEU\Tests\Api;


use PHPUnit\Framework\Assert;

class HotRenderTest extends ApiTestCase {
  const ROUTE = '/api/v1/hotrender';

  protected function setUp(): void {
    parent::setUp();
  }

  public function testRequestNotFound(): void {
    $response = $this->get(self::ROUTE."/made-up-id");
    $this->assertStatus(404, $response);
  }

  public function testRenderHtml(): void {
    $id = uniqid();
    $this->addHotRenderRequestFixture($id, 'example');

    $response = $this->get(self::ROUTE."/{$id}");

    $this->assertStatus(200, $response);
    Assert::assertEquals('example', (string) $response->getBody());
  }

  public function testRenderPdf(): void {
    $id = uniqid();
    $this->addHotRenderRequestFixture($id, 'example');

    $response = $this->get(self::ROUTE."/{$id}?format=pdf");
    $this->assertStatus(200, $response);
    $this->assertContentType('application/pdf', $response);
  }
}
