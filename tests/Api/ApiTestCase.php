<?php


namespace HomeCEU\Tests\Api;


use DateTime;
use HomeCEU\DTS\Api\App;
use HomeCEU\DTS\Api\DiContainer;
use HomeCEU\DTS\Db\Connection;
use HomeCEU\DTS\Persistence;
use HomeCEU\DTS\Persistence\CompiledTemplatePersistence;
use HomeCEU\DTS\Persistence\DocDataPersistence;
use HomeCEU\DTS\Persistence\HotRenderPersistence;
use HomeCEU\DTS\Persistence\TemplatePersistence;
use HomeCEU\DTS\Render\TemplateCompiler;
use HomeCEU\Tests\TestCase as HomeCEUTestCase;
use PHPUnit\Framework\Assert;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Environment;
use Slim\Http\Request;

class ApiTestCase extends HomeCEUTestCase {
  private Persistence $templatePersistence;
  private Persistence $compiledTemplatePersistence;

  protected DiContainer $di;
  protected Connection $db;
  protected App $app;
  protected DocDataPersistence $docDataPersistence;
  protected Persistence $hotRenderPersistence;
  protected string $docType;

  protected function setUp(): void {
    parent::setUp();
    $this->di = new DiContainer();
    $this->db = $this->di->get('dbConnection');
    $this->db->beginTransaction();
    $this->app = new App($this->di);
    $this->docType = uniqid('type_');
  }

  protected function tearDown(): void {
    $this->db->rollback();
    parent::tearDown();
  }

  protected function docDataPersistence(): DocDataPersistence {
    if (empty($this->docDataPersistence)) {
      $this->docDataPersistence = new DocDataPersistence($this->db);
    }
    return $this->docDataPersistence;
  }

  protected function templatePersistence(): TemplatePersistence {
    if (empty($this->templatePersistence)) {
      $this->templatePersistence = new TemplatePersistence($this->db);
    }
    return $this->templatePersistence;
  }

  protected function compiledTemplatePersistence(): CompiledTemplatePersistence {
    if (empty($this->compiledTemplatePersistence)) {
      $this->compiledTemplatePersistence = new CompiledTemplatePersistence($this->db);
    }
    return $this->compiledTemplatePersistence;
  }

  protected function hotRenderPersistence(): HotRenderPersistence {
    if (empty($this->hotRenderPersistence)) {
      $this->hotRenderPersistence = new HotRenderPersistence($this->db);
    }
    return $this->hotRenderPersistence;
  }

  protected function addDocDataFixture($dataKey, $id = null): void {
    $this->docDataPersistence()->persist([
        'docType' => $this->docType,
        'dataKey' => $dataKey,
        'createdAt' => $this->createdAtDateTime(),
        'dataId' => $id ?? uniqid(),
        'data' => ['name'=>'Fred']
    ]);
  }

  protected function addPartialFeature(string $docType, string $templateKey): void {
    $this->templatePersistence()->persist([
        'docType' => $docType . '/partial',
        'templateKey' => $templateKey,
        'createdAt' => $this->createdAtDateTime(),
        'templateId' => uniqid(),
        'body'=> 'this is a partial',
        'author'=>'author',
        'name'=> 'A partial'
    ]);
  }

  protected function addTemplateFixture($templateKey, $id = null, $body = null): void {
    $id = $id ?? uniqid();
    $body = $body ?? 'Hi {{name}}';
    $this->templatePersistence()->persist([
        'docType' => $this->docType,
        'templateKey' => $templateKey,
        'createdAt' => $this->createdAtDateTime(),
        'templateId' => $id,
        'body'=> $body,
        'author'=>'author',
        'name'=>'name'
    ]);
    $this->compiledTemplatePersistence()->persist([
        'templateId' => $id,
        'body' => TemplateCompiler::create()->compile($body)
    ]);
  }

  protected function addHotRenderRequestFixture($requestId, $value): void {
    $this->hotRenderPersistence()->persist([
        'requestId' => $requestId,
        'template' => TemplateCompiler::create()->compile('{{ value }}'),
        'data' => ['value' => $value],
        'createdAt' => new DateTime()
    ]);
  }



  protected function post($uri, array $data): ResponseInterface {
    $method = 'POST';
    $env = Environment::mock([
        'REQUEST_METHOD' => strtoupper($method),
        'REQUEST_URI'    => $uri,
        'CONTENT_TYPE'   => 'application/json'
    ]);
    $req = Request::createFromEnvironment($env)->withParsedBody($data);
    $this->app->getContainer()['request'] = $req;
    return $this->app->run(true);
  }

  protected function get($uri, $headers=[]): ResponseInterface {
    list($uri, $queryParams) = $this->separateUriAndQuery($uri);
    $env = Environment::mock([
        'REQUEST_METHOD' => 'GET',
        'REQUEST_URI'    => $uri,
        'CONTENT_TYPE'   => 'application/json'
    ]);
    $req = Request::createFromEnvironment($env)
        ->withQueryParams($queryParams);
    foreach ($headers as $k=>$v) {
      $req = $req->withHeader($k, $v);
    }
    $this->app->getContainer()['request'] = $req;
    return $this->app->run(true);
  }

  private function separateUriAndQuery($uri): array {
    if (strstr($uri, '?')) {
      list($uri, $queryString) = explode('?', $uri);
      parse_str($queryString, $queryParams);
      return [$uri, $queryParams];
    }
    return [$uri, []];
  }

  protected function head($uri): ResponseInterface {
    $method = 'HEAD';
    $env = Environment::mock([
        'REQUEST_METHOD' => strtoupper($method),
        'REQUEST_URI'    => $uri
    ]);
    $req = Request::createFromEnvironment($env);
    $this->app->getContainer()['request'] = $req;
    return $this->app->run(true);
  }

  protected function getResponseJsonAsObj(ResponseInterface $response): ?\stdClass {
    $obj = json_decode((string) $response->getBody());
    return is_array($obj) ? null : $obj;
  }

  protected function getResponseJsonAsArray(ResponseInterface $response): ?array {
    return json_decode((string) $response->getBody(), $associative = true);
  }

  protected function assertContentType($contentType, ResponseInterface $response): void {
    $headers = $response->getHeaders();
    Assert::assertStringContainsString($contentType, $headers['Content-Type'][0]);
  }

  protected function assertStatus(int $code, ResponseInterface $response): void {
    Assert::assertEquals(
        $code,
        $response->getStatusCode(),
        sprintf(
            "Status %s does not match %s\n Reason: %s",
            $response->getStatusCode(),
            $code,
            $response->getReasonPhrase()
        )
    );
  }
}
