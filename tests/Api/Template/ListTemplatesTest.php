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
 *       "templateId": "3fa85f64-5717-4562-b3fc-2c963f66afa6",
 *       "docType": "courseCompletionCertificate",
 *       "templateKey": "default-ce",
 *       "author": "Robert Martin",
 *       "createdAt": "2020-10-13T11:47:07.259Z",
 *       "bodyUri": "/template/3fa85f64-5717-4562-b3fc-2c963f66afa6"
 *     }
 *   ]
 * }
 */
class ListTemplatesTest extends TestCase {
  const ROUTE = '/api/v1/template';

  protected $data = [];
  protected $expectedResults = [];
  private $expectedExampleResponse = [];

  protected function setUp(): void {
    parent::setUp();
    $this->loadFixtureData();
    // parent is handling db transaction...
  }

  protected function tearDown(): void {
    parent::tearDown();
    // parent is handling db transaction rollback...
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
      array_push($expectedIds, $this->data[$k]['templateId']);
    }

    Assert::assertGreaterThan(count($expectedIds) - 1, $data['items']);
    
    $foundIds = array_column($data['items'], 'templateId');
    foreach ($expectedIds as $id) {
      Assert::assertContains($id, $foundIds);
    }
  }

  protected function loadFixtureData() {
    $exampleId = self::faker()->uuid;
    $this->data = [
        '1:A1' => [
            'templateId' => self::faker()->uuid,
            'docType' => 'type 1',
            'templateKey' => 'key A',
            'name' => self::faker()->sentence,
            'author' => self::faker()->name,
            'createdAt' => new DateTime('2020-01-01'),
            'body' => 'hi {{name}}'
        ],
        '1:A2' => [
            'templateId' => self::faker()->uuid,
            'docType' => 'type 1',
            'templateKey' => 'key A',
            'name' => self::faker()->sentence,
            'author' => self::faker()->name,
            'createdAt' => new DateTime('2020-01-02'),
            'body' => 'hi {{name}}'
        ],
        '1:B1' => [
            'templateId' => self::faker()->uuid,
            'docType' => 'type 1',
            'templateKey' => 'key B',
            'name' => self::faker()->sentence,
            'author' => self::faker()->name,
            'createdAt' => new DateTime('2020-01-03'),
            'body' => 'hi {{name}}'
        ],
        '1:B2' => [
            'templateId' => self::faker()->uuid,
            'docType' => 'type 1',
            'templateKey' => 'key B',
            'name' => self::faker()->sentence,
            'author' => self::faker()->name,
            'createdAt' => new DateTime('2020-01-04'),
            'body' => 'hi {{name}}'
        ],
        '2:C1' => [
            'templateId' => self::faker()->uuid,
            'docType' => 'type 2',
            'templateKey' => 'key C',
            'name' => self::faker()->sentence,
            'author' => self::faker()->name,
            'createdAt' => new DateTime('2020-01-05'),
            'body' => 'hi {{name}}'
        ],
        '2:C2' => [
            'templateId' => self::faker()->uuid,
            'docType' => 'type 2',
            'templateKey' => 'key C',
            'name' => self::faker()->sentence,
            'author' => self::faker()->name,
            'createdAt' => new DateTime('2020-01-06'),
            'body' => 'hi {{name}}'
        ],
        '2:D1' => [
            'templateId' => self::faker()->uuid,
            'docType' => 'type 2',
            'templateKey' => 'key D',
            'name' => self::faker()->sentence,
            'author' => self::faker()->name,
            'createdAt' => new DateTime('2020-01-07'),
            'body' => 'hi {{name}}'
        ],
        '2:D2' => [
            'templateId' => self::faker()->uuid,
            'docType' => 'type 2',
            'templateKey' => 'key D',
            'name' => self::faker()->sentence,
            'author' => self::faker()->name,
            'createdAt' => new DateTime('2020-01-08'),
            'body' => 'hi {{name}}'
        ],
        'find1' => [ // certificate to name bob
            'templateId' => self::faker()->uuid,
            'docType' => 'courseCompletionCertificate',
            'templateKey' => 'how to code',
            'name' => 'what is in a name',
            'author' => 'uncle bob',
            'createdAt' => new DateTime('2020-01-09'),
            'body' => 'hi {{name}}'
        ],
        'find2' => [
            'templateId' => self::faker()->uuid,
            'docType' => 'courseCompletionCertificate',
            'templateKey' => 'how to code',
            'name' => 'what is in a name',
            'author' => 'uncle bob',
            'createdAt' => new DateTime('2020-01-10'),
            'body' => 'hi {{name}}!'
        ],
        'example' => [
            "templateId" => $exampleId,
            "docType" => "example",
            "templateKey" => "default-ce",
            "name" => "is this field even used?",
            "author" => "Robert Martin",
            "createdAt" => new DateTime("2020-10-13 23:47:07"),
            'body' => 'hi {{name}}!'
        ]
    ];

    $this->expectedExampleResponse = [
        "templateId" => $exampleId,
        "docType" => "example",
        "templateKey" => "default-ce",
        "author" => "Robert Martin",
        "createdAt" => "2020-10-13T23:47:07+00:00",
        "bodyUri" => self::ROUTE."/{$exampleId}"
    ];

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
