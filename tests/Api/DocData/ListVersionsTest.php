<?php


namespace HomeCEU\Tests\Api\DocData;

use DateTime;
use HomeCEU\Tests\Api\ApiTestCase;
use PHPUnit\Framework\Assert;

class ListVersionsTest extends ApiTestCase {
  const ROUTE = "/api/v1/docdata";

  public function testListVersions() {
    $dataKey = __FUNCTION__; // just in case it doesnt cleanup, you will know where it came from.
    $this->addFixtureData($dataKey);

    $uri = self::ROUTE."/{$this->docType}/{$dataKey}/history";
    $response = $this->get($uri);
    $responseData = json_decode($response->getBody(), true);

    $this->assertContentType('application/json', $response);
    $this->assertStatus(200, $response);
    $this->assertTotalItems($responseData, 2);
    $this->AssertExpectedVersionItemKeys(
        $responseData,
        ['dataId', 'docType', 'dataKey', 'createdAt']
    );
  }

  public function testResponseFormat() {
    // load fixture
    $key = self::faker()->colorName;
    $id = self::faker()->uuid;

    $this->docDataPersistence()->persist([
        'dataId' => $id,
        'docType' => $this->docType,
        'dataKey' => $key,
        "createdAt" => new DateTime("2020-10-13 23:47:07"),
        'data' => ['name'=>'Fred']
    ]);
    $expected = [
        'total' => 1,
        'items' => [
            [
                'dataId' => $id,
                'docType' => $this->docType,
                'dataKey' => $key,
                "createdAt" => "2020-10-13T23:47:07+00:00",
                "link" => self::ROUTE."/{$id}"
            ]
        ]
    ];

    $uri = self::ROUTE."/{$this->docType}/{$key}/history";
    $response = $this->get($uri);
    $responseData = json_decode($response->getBody(), true);
    Assert::assertEquals($expected, $responseData);
  }

  /**
   * @param string $dataKey
   */
  private function addFixtureData(string $dataKey): void {
    $this->addDocDataFixture($dataKey);
    $this->addDocDataFixture(uniqid());
    $this->addDocDataFixture($dataKey);
  }

  /**
   * @param $responseData
   * @param array $expectedResponseKeys
   */
  private function AssertExpectedVersionItemKeys($responseData, array $expectedResponseKeys): void {
    foreach ($expectedResponseKeys as $key) {
      Assert::assertFalse(empty($responseData['items'][0][$key]));
    }

    Assert::assertFalse(
        array_key_exists('data', $responseData['items'][0]),
        "ERROR: get docdata history should not respond with the data"
    );
  }

  /**
   * @param $responseData array
   * @param $total int
   */
  private function assertTotalItems($responseData, $total): void {
    Assert::assertCount($total, $responseData['items']);
    Assert::assertEquals($total, $responseData['total']);
  }
}
