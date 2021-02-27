<?php declare(strict_types=1);


namespace HomeCEU\Tests\Api;


use PHPUnit\Framework\Assert;

class AddTemplateTest extends TestCase {
  const ROUTE = '/api/v1/template';

  public function testBadRequest(): void {
    $request = [];
    $response = $this->post(self::ROUTE, $request);
    $this->assertStatus(400, $response);
  }

  public function testAddTemplate(): void {
    $request = [
        'docType' => 'DT',
        'templateKey' => 'TK',
        'author' => 'Test Author',
        'body' => 'Hello, {{ name }}!'
    ];
    $response = $this->post(self::ROUTE, $request);
    $this->assertStatus(201, $response);

    $responseData = json_decode((string) $response->getBody(), true);
    Assert::assertEquals($request['docType'], $responseData['docType']);
    Assert::assertEquals($request['templateKey'], $responseData['templateKey']);
    Assert::assertEquals($request['author'], $responseData['author']);
    Assert::assertNotEmpty($responseData['templateId']);
    Assert::assertNotEmpty($responseData['createdAt']);
    Assert::assertNotEmpty($responseData['bodyUri']);
    Assert::assertArrayNotHasKey('body', $responseData);
  }
}
