<?php declare(strict_types=1);


namespace HomeCEU\Tests\Api;


use PHPUnit\Framework\Assert;

class AddHotRenderTest extends ApiTestCase {
  const ROUTE = "/api/v1/hotrender";

  public function testInvalidRequest(): void {
    $data = ['name' => 'example'];
    $response = $this->post(self::ROUTE, $data);

    $this->assertStatus(400, $response);
  }

  public function testAddHotRenderRequest(): void {
    $data = ['template' => 'Hi {{ name }}!', 'data' => ['name' => 'test person']];
    $response = $this->post(self::ROUTE, $data);
    $body = json_decode((string) $response->getBody());
    $headers = $response->getHeaders();

    $this->assertStatus(201, $response);
    Assert::assertEquals(
        $headers['Location'][0],
        self::ROUTE . "/{$body->id}"
    );
  }

  public function testAddHotRenderRequestMissingPartial(): void {
    $data = ['template' => '{{> a_partial }}', 'data' => [], 'docType' => __FUNCTION__];
    $response = $this->post(self::ROUTE, $data);

    $this->assertStatus(409, $response);
  }

  public function testAddHotRenderWithPartial(): void {
    $this->addPartialFixture(__FUNCTION__, 'a_partial');
    $data = ['template' => '{{> a_partial }}', 'data' => [], 'docType' => __FUNCTION__];
    $response = $this->post(self::ROUTE, $data);

    $this->assertStatus(201, $response);
  }
}
