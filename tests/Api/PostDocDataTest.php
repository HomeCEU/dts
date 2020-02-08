<?php


namespace HomeCEU\Tests\Api;


use HomeCEU\DTS\App;
use HomeCEU\Tests\TestCase;
use PHPUnit\Framework\Assert;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Environment;
use Slim\Http\Request;

class PostDocDataTest extends TestCase {
  /** @var App */
  protected $app;

  public function setUp(): void {
    parent::setUp();
    $this->app = $this->slimApp();
  }

  public function testBar() {
    $requestJson = '{"docType":"courseCompletionCertificate","dataKey":"ABC123","data":{"name":"Fred"}}';
    $data = json_decode($requestJson, true);
    $response = $this->post('/docdata', $data);

    Assert::assertSame($response->getStatusCode(), 200);
    $responseData = json_decode((string)$response->getBody(), true);
    $requestData = json_decode($requestJson, true);
    Assert::assertEquals($requestData, $responseData);
    //$this->assertSame((string)$response->getBody(), "Welcome to the Adroit Library Demo.");
  }

  protected function slimApp() {
    $app = new App([]);

    return $app;
  }


  public function post($uri, array $data): ResponseInterface {
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
}