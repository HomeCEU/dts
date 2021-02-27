<?php


namespace HomeCEU\Tests\Api\DocData;

use HomeCEU\Tests\Api\TestCase;

class ExistsTest extends TestCase {
  const ROUTE = "/api/v1/docdata";

  public function testHasData() {
    $dataKey = __FUNCTION__;
    $this->addDocDataFixture($dataKey);
    $response = $this->head(self::ROUTE . "/{$this->docType}/{$dataKey}");
    $this->assertStatus(200, $response);
  }

  public function testDoesntHaveData() {
    $dataKey = __FUNCTION__;
    $response = $this->head(self::ROUTE . "/{$this->docType}/{$dataKey}");
    $this->assertStatus(404, $response);
  }
}
