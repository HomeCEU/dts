<?php


namespace HomeCEU\Tests\Api\DocData;

use DateTime;
use HomeCEU\Tests\Api\ApiTestCase;

class ListVersionsTest extends ApiTestCase {
  const ROUTE = "/api/v1/docdata";

  public function testListVersions() {
    $key = __FUNCTION__; // just in case it doesnt cleanup, you will know where it came from.
    $this->addFixtureData($key);

    $uri = self::ROUTE."/{$this->docType}/{$key}/history";
    $response = $this->get($uri);
    $responseData = json_decode($response->getBody(), true);

    $this->assertContentType('application/json', $response);
    $this->assertStatus(200, $response);
    $this->assertTotalItems($responseData, 2);
    $this->AssertExpectedVersionItemKeys(
        $responseData,
        ['id', 'docType', 'key', 'createdAt']
    );
  }

  public function testResponseFormat() {
    // load fixture
    $key = uniqid();
    $id = uniqid();

    $this->docDataPersistence()->persist([
        'id' => $id,
        'docType' => $this->docType,
        'key' => $key,
        "createdAt" => new DateTime("2020-10-13 23:47:07"),
        'data' => ['name'=>'Fred']
    ]);
    $expected = [
        'total' => 1,
        'items' => [
            [
                'id' => $id,
                'docType' => $this->docType,
                'key' => $key,
                "createdAt" => new DateTime("2020-10-13 23:47:07"),
                "link" => self::ROUTE."/{$id}"
            ]
        ]
    ];

    $uri = self::ROUTE."/{$this->docType}/{$key}/history";
    $response = $this->get($uri);
    $responseData = json_decode($response->getBody(), true);
    $this->assertEquals(json_decode(json_encode($expected), true), $responseData);
  }

  /**
   * @param string $key
   */
  private function addFixtureData(string $key): void {
    $this->addDocDataFixture($key);
    $this->addDocDataFixture(uniqid());
    $this->addDocDataFixture($key);
  }

  /**
   * @param $responseData
   * @param array $expectedResponseKeys
   */
  private function AssertExpectedVersionItemKeys($responseData, array $expectedResponseKeys): void {
    foreach ($expectedResponseKeys as $key) {
      $this->assertFalse(empty($responseData['items'][0][$key]));
    }

    $this->assertArrayNotHasKey('data', $responseData['items'][0]);
  }

  /**
   * @param $responseData array
   * @param $total int
   */
  private function assertTotalItems($responseData, $total): void {
    $this->assertCount($total, $responseData['items']);
    $this->assertEquals($total, $responseData['total']);
  }
}
