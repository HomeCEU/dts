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
    $key = __FUNCTION__;

    $this->assertStatus(404, $this->get(self::ROUTE."/{$this->docType}/{$templateKey}/{$key}"));
  }

  public function testRenderFromKeys(): void {
    $templateKey = __FUNCTION__;
    $key = __FUNCTION__;
    $this->addDocDataFixture($key);
    $this->addTemplateFixture($templateKey);
    $response = $this->get(self::ROUTE."/{$this->docType}/{$templateKey}/{$key}");

    $this->assertStatus(200, $response);
    $this->assertContentType('text/html', $response);
    Assert::assertEquals("Hi Fred", (string) $response->getBody());
  }

  public function testRenderPDFFromKeys(): void {
    $templateKey = __FUNCTION__;
    $key = __FUNCTION__;
    $this->addDocDataFixture($key);
    $this->addTemplateFixture($templateKey);
    $response = $this->get(self::ROUTE."/{$this->docType}/{$templateKey}/{$key}?format=pdf");

    $this->assertStatus(200, $response);
    $this->assertContentType('application/pdf', $response);
  }

  public function testAcceptHeader_pdf() {
    $templateKey = __FUNCTION__;
    $key = __FUNCTION__;
    $this->addDocDataFixture($key);
    $this->addTemplateFixture($templateKey);

    $response = $this->get(
        self::ROUTE."/{$this->docType}/{$templateKey}/{$key}",
        ['Accept' => 'application/pdf,text/html;q=0.9,*/*;q=0.8']
    );

    $this->assertStatus(200, $response);
    $this->assertContentType('application/pdf', $response);
  }

  public function testRenderFromQuery_TemplateKey_DataKey() {
    $this->loadFixtures();
    $templateKey = 'T';
    $key = 'D';
    $uri = self::ROUTE."?docType={$this->docType}&templateKey={$templateKey}&key={$key}";
    $responseBody = $this->httpGetRender($uri);
    Assert::assertEquals("Hi Smith, Jane", $responseBody);
  }

  public function testRenderFromQuery_TemplateKey_DataId() {
    $fixtures = $this->loadFixtures();
    $templateKey = 'T';
    $id = $fixtures['docData'][0]['id'];
    $uri = self::ROUTE."?docType={$this->docType}&templateKey={$templateKey}&id={$id}";
    $responseBody = $this->httpGetRender($uri);
    Assert::assertEquals("Hi Doe, Jane", $responseBody);
  }

  public function testRenderFromQuery_TemplateId_DataKey() {
    $fixtures = $this->loadFixtures();
    $key = 'D';
    $templateId = $fixtures['template'][0]['id'];
    $uri = self::ROUTE."?docType={$this->docType}&templateId={$templateId}&key={$key}";
    $responseBody = $this->httpGetRender($uri);
    Assert::assertEquals("Hi Jane Smith", $responseBody);
  }

  public function testRenderFromQuery_TemplateId_DataId() {
    $fixtures = $this->loadFixtures();
    $templateId = $fixtures['template'][0]['id'];
    $id = $fixtures['docData'][0]['id'];
    $uri = self::ROUTE."?docType={$this->docType}&templateId={$templateId}&id={$id}";
    $responseBody = $this->httpGetRender($uri);
    Assert::assertEquals("Hi Jane Doe", $responseBody);
  }

  public function testRenderFromQuery_Pdf() {
    $this->loadFixtures();
    $templateKey = 'T';
    $key = 'D';
    $uri = self::ROUTE."?docType={$this->docType}&templateKey={$templateKey}&key={$key}";
    $response = $this->get($uri."&format=pdf");
    $this->assertStatus(200, $response);
    $this->assertContentType('application/pdf', $response);
  }



  public function testRenderFromQuery_404() {
    $fixtures = $this->loadFixtures();
    $templateId = $fixtures['template'][0]['id'];
    $id = $fixtures['docData'][0]['id'];
    $templateKey = 'T';
    $key = 'D';
    $fake = 'i-dont-exist';
    $uris = [
        "/render?docType={$this->docType}&templateKey={$templateKey}&key={$fake}",
        "/render?docType={$this->docType}&templateKey={$fake}&key={$key}",
        "/render?docType={$this->docType}&templateId={$templateId}&id={$fake}",
        "/render?docType={$this->docType}&templateId={$fake}&id={$id}"
    ];
    foreach ($uris as $uri) {
      $response = $this->get($uri);
      $this->assertStatus(404, $response);
    }
  }

  public function testRenderFromQuery_InvalidRequest() {
    $fixtures = $this->loadFixtures();
    $templateId = $fixtures['template'][0]['id'];
    $id = $fixtures['docData'][0]['id'];
    $templateKey = 'T';
    $key = 'D';
    $uris = [
        self::ROUTE."?docType={$this->docType}&templateKey={$templateKey}", // data Key or Id required
        self::ROUTE."?docType={$this->docType}&key={$key}",         // template Key or Id required
        self::ROUTE."?docType={$this->docType}&templateId={$templateId}",   // data Key or Id required
        self::ROUTE."?docType={$this->docType}&id={$id}",           // template Key or Id required
        self::ROUTE."?templateKey={$templateKey}&key={$key}",       // docType required
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
        ['id' => self::faker()->uuid, 'key'=>'D', 'data' => ['fname'=>'Jane', 'lname'=>'Doe']],
        ['id' => self::faker()->uuid, 'key'=>'D', 'data' => ['fname'=>'Jane', 'lname'=>'Smith']],
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
