<?php


namespace HomeCEU\Tests\Api\Template;

use DateTime;
use PHPUnit\Framework\Assert;

/**
 * Class ListTemplatesTest
 * @package HomeCEU\Tests\Api\Template
 * These are acceptance tests for https://homeceu.github.io/dts-docs/#/Templates/getTemplates
 * /template
 * /template?filter[type]=someDocType
 * /template?filter[search]=searchString
 *
 * Example Response:
 * {
 *   "total": 1,
 *   "items": [
 *     {
 *       "id": "3fa85f64-5717-4562-b3fc-2c963f66afa6",
 *       "docType": "courseCompletionCertificate",
 *       "key": "default-ce",
 *       "author": "Robert Martin",
 *       "createdAt": "2020-10-13T11:47:07.259Z",
 *       "bodyUri": "/template/3fa85f64-5717-4562-b3fc-2c963f66afa6"
 *     }
 *   ]
 * }
 */
class ListTemplatesTest extends TestCase {
  const ROUTE = '/api/v1/template';

  protected array $data = [];
  protected array $expectedResults = [];
  private array $expectedExampleResponse = [];

  protected function setUp(): void {
    parent::setUp();
    $this->loadFixtureData();
  }

  protected function tearDown(): void {
    parent::tearDown();
  }

  public function testListAllTemplates() {
    $data = $this->httpGetTemplatesFromUri(self::ROUTE);
    $this->assertExpectedResults($data, 'all');
  }

  public function testListTemplatesByDoctype() {
    $types = ['type 1', 'type 2'];
    foreach ($types as $type) {
      $data = $this->httpGetTemplatesFromUri(self::ROUTE."?filter[type]={$type}");
      $this->assertExpectedResults($data, $type);
    }
  }

  public function testListTemplatesBySearchString() {
    $searchString = "certificate to name bob";

    $data = $this->httpGetTemplatesFromUri(self::ROUTE."?filter[search]={$searchString}");
    $this->assertExpectedResults($data, "find");
  }

  public function testTemplateResponseFormat() {
    $data = $this->httpGetTemplatesFromUri(self::ROUTE."?filter[type]=example");
    $actualStruct = $data['items'][0];
    $this->assertEquals($this->expectedExampleResponse, $actualStruct);
  }

  protected function assertExpectedResults($data, $key) {
    $expectedIds = [];
    foreach ($this->expectedResults[$key] as $k) {
      array_push($expectedIds, $this->data[$k]['id']);
    }

    Assert::assertGreaterThan(count($expectedIds) - 1, $data['items']);
    
    $foundIds = array_column($data['items'], 'id');
    foreach ($expectedIds as $id) {
      Assert::assertContains($id, $foundIds);
    }
  }

  protected function loadFixtureData() {
    $exampleId = self::faker()->uuid;
    $this->data = [
        '1:A1' => [
            'id' => self::faker()->uuid,
            'docType' => 'type 1',
            'key' => 'key A',
            'name' => self::faker()->sentence,
            'author' => self::faker()->name,
            'createdAt' => new DateTime('2020-01-01'),
            'body' => 'hi {{name}}'
        ],
        '1:A2' => [
            'id' => self::faker()->uuid,
            'docType' => 'type 1',
            'key' => 'key A',
            'name' => self::faker()->sentence,
            'author' => self::faker()->name,
            'createdAt' => new DateTime('2020-01-02'),
            'body' => 'hi {{name}}'
        ],
        '1:B1' => [
            'id' => self::faker()->uuid,
            'docType' => 'type 1',
            'key' => 'key B',
            'name' => self::faker()->sentence,
            'author' => self::faker()->name,
            'createdAt' => new DateTime('2020-01-03'),
            'body' => 'hi {{name}}'
        ],
        '1:B2' => [
            'id' => self::faker()->uuid,
            'docType' => 'type 1',
            'key' => 'key B',
            'name' => self::faker()->sentence,
            'author' => self::faker()->name,
            'createdAt' => new DateTime('2020-01-04'),
            'body' => 'hi {{name}}'
        ],
        '2:C1' => [
            'id' => self::faker()->uuid,
            'docType' => 'type 2',
            'key' => 'key C',
            'name' => self::faker()->sentence,
            'author' => self::faker()->name,
            'createdAt' => new DateTime('2020-01-05'),
            'body' => 'hi {{name}}'
        ],
        '2:C2' => [
            'id' => self::faker()->uuid,
            'docType' => 'type 2',
            'key' => 'key C',
            'name' => self::faker()->sentence,
            'author' => self::faker()->name,
            'createdAt' => new DateTime('2020-01-06'),
            'body' => 'hi {{name}}'
        ],
        '2:D1' => [
            'id' => self::faker()->uuid,
            'docType' => 'type 2',
            'key' => 'key D',
            'name' => self::faker()->sentence,
            'author' => self::faker()->name,
            'createdAt' => new DateTime('2020-01-07'),
            'body' => 'hi {{name}}'
        ],
        '2:D2' => [
            'id' => self::faker()->uuid,
            'docType' => 'type 2',
            'key' => 'key D',
            'name' => self::faker()->sentence,
            'author' => self::faker()->name,
            'createdAt' => new DateTime('2020-01-08'),
            'body' => 'hi {{name}}'
        ],
        'find1' => [ // certificate to name bob
            'id' => self::faker()->uuid,
            'docType' => 'courseCompletionCertificate',
            'key' => 'how to code',
            'name' => 'what is in a name',
            'author' => 'uncle bob',
            'createdAt' => new DateTime('2020-01-09'),
            'body' => 'hi {{name}}'
        ],
        'find2' => [
            'id' => self::faker()->uuid,
            'docType' => 'courseCompletionCertificate',
            'key' => 'how to code',
            'name' => 'what is in a name',
            'author' => 'uncle bob',
            'createdAt' => new DateTime('2020-01-10'),
            'body' => 'hi {{name}}!'
        ],
        'example' => [
            "id" => $exampleId,
            "docType" => "example",
            "key" => "default-ce",
            "name" => "is this field even used?",
            "author" => "Robert Martin",
            "createdAt" => new DateTime("2020-10-13 23:47:07"),
            'body' => 'hi {{name}}!'
        ]
    ];

    $this->expectedExampleResponse = json_decode(json_encode([
        "id" => $exampleId,
        "docType" => "example",
        "key" => "default-ce",
        "author" => "Robert Martin",
        "createdAt" => new DateTime("2020-10-13 23:47:07"),
        "bodyUri" => self::ROUTE."/{$exampleId}"
    ]), true);

    $p = $this->templatePersistence();
    foreach ($this->data as $row) {
      $p->persist($row);
    }

    $this->expectedResults = [
        'all' => [
            '1:A2',
            '1:B2',
            '2:C2',
            '2:D2',
            'find2',
            'example'
        ],
        'type 1' => [
            '1:A2',
            '1:B2'
        ],
        'type 2' => [
            '2:C2',
            '2:D2'
        ],
        'find' => [
            'find2'
        ]
    ];
  }
}
