<?php


namespace HomeCEU\Tests\Api\Template;

use DateTime;
use PHPUnit\Framework\Assert;

class TemplateVersionsTest extends TestCase {
  const ROUTE = '/api/v1/template';

  protected string $type = 'TemplateVersionsTest';
  protected string $key;
  protected array $data = [];
  protected array $expectedResults = [];
  private array $expectedExampleResponse;
  private string $exampleKey;

  protected function setUp(): void {
    parent::setUp();
    $this->loadFixtureData();
  }

  protected function tearDown(): void {
    parent::tearDown();
  }

  // test GET /template/{type}/{key}/history
  public function testGetTemplateHistory() {
    $data = $this->httpGetTemplatesFromUri(self::ROUTE."/{$this->type}/{$this->key}/history");
    $this->assertExpectedResults($data, "versions");
  }

  public function testTemplateResponseFormat() {
    $data = $this->httpGetTemplatesFromUri(self::ROUTE."/{$this->type}/{$this->exampleKey}/history");
    $actualStruct = $data['items'][0];
    $this->assertEquals($this->expectedExampleResponse, $actualStruct);
  }

  protected function assertExpectedResults($data, $key) {
    $expectedIds = [];
    foreach ($this->expectedResults[$key] as $k) {
      array_push($expectedIds, $this->data[$k]['id']);
    }

    Assert::assertSameSize($expectedIds, $data['items']);

    foreach ($data['items'] as $row) {
      Assert::assertContains($row['id'], $expectedIds);
      Assert::assertArrayNotHasKey('body', $row);
    }
  }

  protected function loadFixtureData() {
    $this->key = self::faker()->firstName;
    $exampleId = self::faker()->uuid;
    $this->exampleKey = "example-".uniqid();
    $this->data = [
        'v1' => [
            'id' => self::faker()->uuid,
            'docType' => $this->type,
            'key' => $this->key,
            'name' => self::faker()->sentence,
            'author' => self::faker()->name,
            'createdAt' => new DateTime('2020-01-01'),
            'body' => 'hi {{name}}'
        ],
        'v2' => [
            'id' => self::faker()->uuid,
            'docType' => $this->type,
            'key' => $this->key,
            'name' => self::faker()->sentence,
            'author' => self::faker()->name,
            'createdAt' => new DateTime('2020-01-02'),
            'body' => 'hi {{name}}'
        ],
        'v3' => [
            'id' => self::faker()->uuid,
            'docType' => $this->type,
            'key' => $this->key,
            'name' => self::faker()->sentence,
            'author' => self::faker()->name,
            'createdAt' => new DateTime('2020-01-03'),
            'body' => 'hi {{name}}'
        ],
        'other' => [
            'id' => self::faker()->uuid,
            'docType' => $this->type,
            'key' => self::faker()->lastName,
            'name' => self::faker()->sentence,
            'author' => self::faker()->name,
            'createdAt' => new DateTime('2020-01-04'),
            'body' => 'hi {{name}}'
        ],
        'example' => [
            "id" => $exampleId,
            "docType" => $this->type,
            "key" => $this->exampleKey,
            "name" => "is this field even used?",
            "author" => "Robert Martin",
            "createdAt" => new DateTime("2020-10-13 23:47:07"),
            'body' => 'hi {{name}}!'
        ]
    ];

    // php \DateTime doesn't compare to a \DateTime object that's been converted to JSON and back
    // the expected response will have been a decoded json string
    $this->expectedExampleResponse = json_decode(json_encode([
        "id" => $exampleId,
        "docType" => $this->type,
        "key" => $this->exampleKey,
        "author" => "Robert Martin",
        "createdAt" => new DateTime("2020-10-13 23:47:07"),
        "bodyUri" => self::ROUTE."/{$exampleId}"
    ]), true);

    foreach ($this->data as $row) {
      $this->templatePersistence()->persist($row);
    }

    $this->expectedResults = [
        'versions' => [
            'v1','v2','v3'
        ],
    ];
  }
}
