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
    $id = $this->fixtureData['example']['id'];
    $responseData = $this->httpGet(self::ROUTE."/{$id}");
    Assert::assertEquals($this->expectedExampleResponse, $responseData);
  }

  public function testGetById_Expect404() {
    $uri = self::ROUTE."/no-such-id";
    $response = $this->get($uri);
    $this->assertStatus(404, $response);
  }

  public function testGetByKey() {
    $responseData = $this->httpGet(self::ROUTE."/{$this->docType}/A");
    // expect the most recent id from key A
    $expectedId = $this->fixtureData['A2']['id'];
    Assert::assertEquals($expectedId, $responseData['id']);
  }

  public function testGetByKey_Expect404() {
    $uri = self::ROUTE."/{$this->docType}/no-such-key";
    $response = $this->get($uri);
    $this->assertStatus(404, $response);
  }

  public function testGetByKey_responseFormat() {
    $example = $this->fixtureData['example'];
    $responseData = $this->httpGet(self::ROUTE."/{$example["docType"]}/{$example['key']}");
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
            'id' => self::faker()->uuid,
            'docType' => $this->docType,
            'key' => 'A',
            'createdAt' => new DateTime('2020-01-0'.++$day),
            'data' => ['name'=>self::faker()->name]
        ],
        'A2' => [
            'id' => self::faker()->uuid,
            'docType' => $this->docType,
            'key' => 'A',
            'createdAt' => new DateTime('2020-01-0'.++$day),
            'data' => ['name'=>self::faker()->name]
        ],
        'find' => $this->docDataArray([
            'id' => self::faker()->uuid,
            'docType' => $this->docType
        ]),
        'example' => [
            'id' => $exampleId,
            'docType' => $this->docType,
            'key' => 'example',
            "createdAt" => new DateTime("2020-10-13 23:47:07"),
            'data' => ['name'=>'joe']
        ]
    ];

    // In order to compare to the response
    // we need to encode this data to json, and then decode it
    // this ensures our expected response createdAt matches the real response
    $this->expectedExampleResponse = json_decode(json_encode([
        'id' => $exampleId,
        'docType' => $this->docType,
        'key' => 'example',
        "createdAt" => new DateTime("2020-10-13 23:47:07"),
        'data' => ['name'=>'joe']
    ]), true);

    foreach ($this->fixtureData as $r) {
      $this->docDataPersistence()->persist($this->docDataArray($r));
    }
  }
}
