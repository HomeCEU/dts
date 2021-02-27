<?php


namespace HomeCEU\Tests\Api\Template;


use PHPUnit\Framework\Assert;

class ListDocTypesTest extends TestCase {
  const ROUTE = '/api/v1/doctype';

  protected function setUp(): void {
    parent::setUp();
    // parent is handling db transaction...
  }

  protected function tearDown(): void {
    parent::tearDown();
    // parent is handling db transaction rollback...
  }

  public function testInvoke() {
    // Load Fixture Data
    $fixtureData = [
        [ 'docType' => 'A', 'templateKey' => 'k1' ], // 3 versions of k1
        [ 'docType' => 'A', 'templateKey' => 'k1' ],
        [ 'docType' => 'A', 'templateKey' => 'k1' ],
        [ 'docType' => 'A' ], // will be unique key
        [ 'docType' => 'A' ], // so should be 3 A's
        [ 'docType' => 'B' ],
        [ 'docType' => 'B' ], // 2 B's
        [ 'docType' => 'C' ], // 1 C
    ];
    $expectedDoctypeCounts = [
        'A' => 3,
        'B' => 2,
        'C' => 1
    ];
    $expectedResponseForA = [
        "docType" => 'A',
        "templateCount" => 3,
        "links" => [
            "templates" => "/template?filter[type]=A"
        ]
    ];
    $p = $this->templatePersistence();
    foreach ($fixtureData as $data) {
      $p->persist($this->templateArray($data));
    }

    // Action
    $responseData = $this->httpGetDocTypes();
    $docTypes = $responseData['items'];

    // Asserts
    Assert::assertSameSize($expectedDoctypeCounts, $docTypes);
    foreach ($docTypes as $row) {
      Assert::assertArrayHasKey($row['docType'], $expectedDoctypeCounts);
      Assert::assertEquals(
          $expectedDoctypeCounts[$row['docType']],
          $row['templateCount']
      );
      if ($row['docType'] == 'A') {
        Assert::assertEquals($expectedResponseForA, $row);
      }
    }
  }

  public function testNoDocTypes() {
    $responseData = $this->httpGetDocTypes();
    $expected = [
        "total" => 0,
        "items" => []
    ];
    $this->assertEquals($expected, $responseData);
  }

  protected function httpGetDocTypes() {

    $response = $this->get(self::ROUTE);
    $responseData = json_decode($response->getBody(), true);

    // Assertions
    $this->assertStatus(200, $response);
    $this->assertContentType('application/json', $response);
    Assert::assertArrayHasKey('total', $responseData);
    Assert::assertArrayHasKey('items', $responseData);
    Assert::assertIsArray($responseData['items']);
    Assert::assertCount($responseData['total'], $responseData['items']);

    return $responseData;
  }
}
