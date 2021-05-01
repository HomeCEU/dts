<?php declare(strict_types=1);


namespace HomeCEU\Tests\Api;


class StatusTest extends ApiTestCase {
  public function testStatusReturnsOk(): void {
    $response = $this->get('/api/v1/status');
    $this->assertStatus(200, $response);
  }
}
