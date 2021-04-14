<?php


namespace HomeCEU\Tests\Api\DocData;

use DateTime;
use PHPUnit\Framework\Assert;

class GetDocDataTest extends TestCase {
  const ROUTE = "/api/v1/docdata";

  /**
   * @var array
   */
  private $fixtureData;
  /**
   * @var array
   */
  private $expectedExampleResponse;

  protected function setUp(): void {
    parent::setUp();
    $this->loadFixtureData();
    // parent is handling db transaction...
  }

  protected function tearDown(): void {
    parent::tearDown();
    // parent is handling db transaction rollback...
  }

  public function testGetById() {
    $dataId = $this->fixtureData['example']['dataId'];
    $responseData = $this->httpGet(self::ROUTE."/{$dataId}");
    Assert::assertEquals($this->expectedExampleResponse, $responseData);
  }

  public function testGetById_Expect404() {
    $uri = self::ROUTE."/no-such-id";
    $response = $this->get($uri);
    $this->assertStatus(404, $response);
  }

  public function testGetByKey() {
    $responseData = $this->httpGet(self::ROUTE."/{$this->docType}/A");
    // expect the most recent id from dataKey A
    $expectedId = $this->fixtureData['A2']['dataId'];
    Assert::assertEquals($expectedId, $responseData['dataId']);
  }

  public function testGetByKey_Expect404() {
    $uri = self::ROUTE."/{$this->docType}/no-such-key";
    $response = $this->get($uri);
    $this->assertStatus(404, $response);
  }

  public function testGetByKey_responseFormat() {
    $example = $this->fixtureData['example'];
    $responseData = $this->httpGet(self::ROUTE."/{$example["docType"]}/{$example['dataKey']}");
    Assert::assertEquals($this->expectedExampleResponse, $responseData);
  }

  protected function httpGet($uri) {
    $response = $this->get($uri);
    $responseData = json_decode($this->get($uri)->getBody(), true);
    $this->assertStatus(200, $response);
    $this->assertContentType('application/json', $response);
    return $responseData;
  }

  protected function loadFixtureData() {
    $day = 0;
    $exampleId = self::faker()->uuid;
    $this->fixtureData = [
        'A1' => [
            'dataId' => self::faker()->uuid,
            'docType' => $this->docType,
            'dataKey' => 'A',
            'createdAt' => new DateTime('2020-01-0'.++$day),
            'data' => ['name'=>self::faker()->name]
        ],
        'A2' => [
            'dataId' => self::faker()->uuid,
            'docType' => $this->docType,
            'dataKey' => 'A',
            'createdAt' => new DateTime('2020-01-0'.++$day),
            'data' => ['name'=>self::faker()->name]
        ],
        'find' => $this->docDataArray([
            'dataId' => self::faker()->uuid,
            'docType' => $this->docType
        ]),
        'example' => [
            'dataId' => $exampleId,
            'docType' => $this->docType,
            'dataKey' => 'example',
            "createdAt" => new DateTime("2020-10-13 23:47:07"),
            'data' => ['name'=>'joe']
        ]
    ];

    $this->expectedExampleResponse = [
        'dataId' => $exampleId,
        'docType' => $this->docType,
        'dataKey' => 'example',
        "createdAt" => "2020-10-13T23:47:07+00:00",
        'data' => ['name'=>'joe']
    ];

    foreach ($this->fixtureData as $r) {
      $this->docDataPersistence()->persist($this->docDataArray($r));
    }
  }
}
