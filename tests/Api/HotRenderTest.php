<?php declare(strict_types=1);


namespace HomeCEU\Tests\Api;


use HomeCEU\Tests\Api\TestCase;
use PHPUnit\Framework\Assert;

class HotRenderTest extends TestCase {
  const ROUTE = '/api/v1/hotrender';

  protected function setUp(): void {
    parent::setUp();
  }

  public function testRequestNotFound(): void {
    $response = $this->get(self::ROUTE."/made-up-id");
    $this->assertStatus(404, $response);
  }

  public function testRenderHtml(): void {
    $requestId = uniqid();
    $this->addHotRenderRequestFixture($requestId, 'example');

    $response = $this->get(self::ROUTE."/{$requestId}");

    $this->assertStatus(200, $response);
    Assert::assertEquals('example', (string) $response->getBody());
  }

  public function testRenderPdf(): void {
    $requestId = uniqid();
    $this->addHotRenderRequestFixture($requestId, 'example');

    $response = $this->get(self::ROUTE."/{$requestId}?format=pdf");
    $this->assertStatus(200, $response);
    $this->assertContentType('application/pdf', $response);
  }
}
