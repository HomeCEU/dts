<?php declare(strict_types=1);


namespace HomeCEU\Tests\Api\Middleware;

use HomeCEU\Tests\Api\ApiTestCase;

class OptionsMiddlewareTest extends ApiTestCase {
  /**
   * any OPTIONS request should return a 204 with an empty body
   *
   * @dataProvider routes
   */
  public function testRouteReturns204WithAnEmptyBody(string $route): void {
    $response = $this->options("/api/v1{$route}", []);
    $this->assertStatus(204, $response);
    $this->assertEmpty((string) $response->getBody());
  }

  public function routes(): \Generator {
    yield ['/status'];
    yield ['/template'];
    yield ['/docdata'];
  }
}
