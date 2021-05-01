<?php


namespace HomeCEU\Tests\Api\Template;


use PHPUnit\Framework\Assert;

class ListDocTypesTest extends TestCase {
  const ROUTE = '/api/v1/doctype';

  protected function setUp(): void {
    parent::setUp();
  }

  protected function tearDown(): void {
    parent::tearDown();
  }

  public function testInvoke() {
    // Load Fixture Data
    $fixtureData = [
        [ 'docType' => 'A', 'key' => 'k1' ], // 3 versions of k1
        [ 'docType' => 'A', 'key' => 'k1' ],
        [ 'docType' => 'A', 'key' => 'k1' ],
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
    $this->loadTemplateFixtures($fixtureData);

    $responseData = $this->httpGetDocTypes();
    $foundDocTypeObjs = $responseData['items'];
    foreach (array_column($foundDocTypeObjs, 'docType') as $k => $docType) {
      if (isset($expectedDoctypeCounts[$docType])) {
        Assert::assertEquals($expectedDoctypeCounts[$docType], $foundDocTypeObjs[$k]['templateCount']);
      }
    }
    $this->assertProperObjectStructures($foundDocTypeObjs);
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

  private function loadTemplateFixtures(array $fixtureData): void {
    $p = $this->templatePersistence();
    foreach ($fixtureData as $data) {
      $p->persist($this->templateArray($data));
    }
  }

  private function assertProperObjectStructures($foundDocTypeObjs): void {
    foreach ($foundDocTypeObjs as $foundDocTypeObj) {
      Assert::assertArrayHasKey('docType', $foundDocTypeObj);
      Assert::assertArrayHasKey('templateCount', $foundDocTypeObj);
      Assert::assertArrayHasKey('links', $foundDocTypeObj);
      Assert::assertArrayHasKey('templates', $foundDocTypeObj['links']);
    }
  }
}
