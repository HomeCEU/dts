<?php declare(strict_types=1);


namespace HomeCEU\Tests\Api\Middleware;

use HomeCEU\Tests\Api\ApiTestCase;

/**
 * Class CORSMiddlewareTest
 *
 * This tests that the API middleware is providing a proper header for CORS.
 *
 * @author Dan McAdams
 * @package HomeCEU\Tests\Api
 */
class CORSMiddlewareTest extends ApiTestCase {
  public function testRoutesHaveProperCORSSettings(): void {
    $response = $this->get('/api/v1/status', ['HTTP_ORIGIN' => 'https://example.com']);
    $this->assertStatus(200, $response);
    $this->assertHeaderHasValue('Access-Control-Allow-Origin', 'https://example.com', $response);
    $this->assertHeaderHasValue('Access-Control-Allow-Credentials', 'true', $response);
    $this->assertHeaderHasValue('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept', $response);
  }
}
