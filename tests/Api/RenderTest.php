<?php


namespace HomeCEU\Tests\Api;

use PHPUnit\Framework\Assert;

class RenderTest extends ApiTestCase {
  const ROUTE = '/api/v1/render';

  protected function setUp(): void {
    parent::setUp();
  }

  public function testTemplateNotFound(): void {
    $templateKey = __FUNCTION__;
    $dataKey = __FUNCTION__;

    $this->assertStatus(404, $this->get(self::ROUTE."/{$this->docType}/{$templateKey}/{$dataKey}"));
  }

  public function testRenderFromKeys(): void {
    $templateKey = __FUNCTION__;
    $dataKey = __FUNCTION__;
    $this->addDocDataFixture($dataKey);
    $this->addTemplateFixture($templateKey);
    $response = $this->get(self::ROUTE."/{$this->docType}/{$templateKey}/{$dataKey}");

    $this->assertStatus(200, $response);
    $this->assertContentType('text/html', $response);
    Assert::assertEquals("Hi Fred", (string) $response->getBody());
  }

  public function testRenderPDFFromKeys(): void {
    $templateKey = __FUNCTION__;
    $dataKey = __FUNCTION__;
    $this->addDocDataFixture($dataKey);
    $this->addTemplateFixture($templateKey);
    $response = $this->get(self::ROUTE."/{$this->docType}/{$templateKey}/{$dataKey}?format=pdf");

    $this->assertStatus(200, $response);
    $this->assertContentType('application/pdf', $response);
  }

  public function testAcceptHeader_pdf() {
    $templateKey = __FUNCTION__;
    $dataKey = __FUNCTION__;
    $this->addDocDataFixture($dataKey);
    $this->addTemplateFixture($templateKey);

    $response = $this->get(
        self::ROUTE."/{$this->docType}/{$templateKey}/{$dataKey}",
        ['Accept' => 'application/pdf,text/html;q=0.9,*/*;q=0.8']
    );

    $this->assertStatus(200, $response);
    $this->assertContentType('application/pdf', $response);
  }

  public function testRenderFromQuery_TemplateKey_DataKey() {
    $this->loadFixtures();
    $templateKey = 'T';
    $dataKey = 'D';
    $uri = self::ROUTE."?docType={$this->docType}&templateKey={$templateKey}&dataKey={$dataKey}";
    $responseBody = $this->httpGetRender($uri);
    Assert::assertEquals("Hi Smith, Jane", $responseBody);
  }

  public function testRenderFromQuery_TemplateKey_DataId() {
    $fixtures = $this->loadFixtures();
    $templateKey = 'T';
    $dataId = $fixtures['docData'][0]['dataId'];
    $uri = self::ROUTE."?docType={$this->docType}&templateKey={$templateKey}&dataId={$dataId}";
    $responseBody = $this->httpGetRender($uri);
    Assert::assertEquals("Hi Doe, Jane", $responseBody);
  }

  public function testRenderFromQuery_TemplateId_DataKey() {
    $fixtures = $this->loadFixtures();
    $dataKey = 'D';
    $templateId = $fixtures['template'][0]['id'];
    $uri = self::ROUTE."?docType={$this->docType}&templateId={$templateId}&dataKey={$dataKey}";
    $responseBody = $this->httpGetRender($uri);
    Assert::assertEquals("Hi Jane Smith", $responseBody);
  }

  public function testRenderFromQuery_TemplateId_DataId() {
    $fixtures = $this->loadFixtures();
    $templateId = $fixtures['template'][0]['id'];
    $dataId = $fixtures['docData'][0]['dataId'];
    $uri = self::ROUTE."?docType={$this->docType}&templateId={$templateId}&dataId={$dataId}";
    $responseBody = $this->httpGetRender($uri);
    Assert::assertEquals("Hi Jane Doe", $responseBody);
  }

  public function testRenderFromQuery_Pdf() {
    $this->loadFixtures();
    $templateKey = 'T';
    $dataKey = 'D';
    $uri = self::ROUTE."?docType={$this->docType}&templateKey={$templateKey}&dataKey={$dataKey}";
    $response = $this->get($uri."&format=pdf");
    $this->assertStatus(200, $response);
    $this->assertContentType('application/pdf', $response);
  }



  public function testRenderFromQuery_404() {
    $fixtures = $this->loadFixtures();
    $templateId = $fixtures['template'][0]['id'];
    $dataId = $fixtures['docData'][0]['dataId'];
    $templateKey = 'T';
    $dataKey = 'D';
    $fake = 'i-dont-exist';
    $uris = [
        "/render?docType={$this->docType}&templateKey={$templateKey}&dataKey={$fake}",
        "/render?docType={$this->docType}&templateKey={$fake}&dataKey={$dataKey}",
        "/render?docType={$this->docType}&templateId={$templateId}&dataId={$fake}",
        "/render?docType={$this->docType}&templateId={$fake}&dataId={$dataId}"
    ];
    foreach ($uris as $uri) {
      $response = $this->get($uri);
      $this->assertStatus(404, $response);
    }
  }

  public function testRenderFromQuery_InvalidRequest() {
    $fixtures = $this->loadFixtures();
    $templateId = $fixtures['template'][0]['id'];
    $dataId = $fixtures['docData'][0]['dataId'];
    $templateKey = 'T';
    $dataKey = 'D';
    $uris = [
        self::ROUTE."?docType={$this->docType}&templateKey={$templateKey}", // data Key or Id required
        self::ROUTE."?docType={$this->docType}&dataKey={$dataKey}",         // template Key or Id required
        self::ROUTE."?docType={$this->docType}&templateId={$templateId}",   // data Key or Id required
        self::ROUTE."?docType={$this->docType}&dataId={$dataId}",           // template Key or Id required
        self::ROUTE."?templateKey={$templateKey}&dataKey={$dataKey}",       // docType required
    ];
    foreach ($uris as $uri) {
      $response = $this->get($uri);
      $this->assertStatus(400, $response);
    }
  }

  protected function httpGetRender($uri) {
    $response = $this->get($uri);
    $this->assertStatus(200, $response);
    $this->assertContentType('text/html', $response);
    return strval($response->getBody());
  }

  protected function loadFixtures() {
    $templates = [
        // 2 templates with the same key to ensure we get the newest one...
        ['id' => self::faker()->uuid, 'key'=>'T', 'body'=>'Hi {{fname}} {{lname}}'],
        ['id' => self::faker()->uuid, 'key'=>'T', 'body'=>'Hi {{lname}}, {{fname}}']
    ];
    foreach ($templates as $r) {
      $this->addTemplateFixture($r['key'], $r['id'], $r['body']);
    }

    $data = [
        // 2 data with same key to ensure we get the newest one...
        ['dataId' => self::faker()->uuid, 'dataKey'=>'D', 'data' => ['fname'=>'Jane', 'lname'=>'Doe']],
        ['dataId' => self::faker()->uuid, 'dataKey'=>'D', 'data' => ['fname'=>'Jane', 'lname'=>'Smith']],
    ];
    foreach ($data as $r) {
      $r['docType'] = $this->docType;
      $this->docDataPersistence()->persist(
        $this->docDataArray($r)
      );
    }

    return [
        'template' => $templates,
        'docData' => $data
    ];
  }
}
