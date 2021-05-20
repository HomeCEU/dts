<?php declare(strict_types=1);


namespace HomeCEU\Tests\Api;


use PHPUnit\Framework\Assert;
use Psr\Http\Message\ResponseInterface;

trait ApiAssertions {
  protected function assertHeaderHasValue(string $header, ?string $expected, ResponseInterface $response): void {
    $actual = $response->getHeader($header);
    $impl = implode(';', $actual);
    Assert::assertContains($expected, $actual, "Header '{$impl}' does not contain '{$expected}'");
  }

  protected function assertContentType($expected, ResponseInterface $response): void {
    $headers = $response->getHeaders();
    Assert::assertStringContainsString($expected, $headers['Content-Type'][0]);
  }

  protected function assertStatus(int $expected, ResponseInterface $response): void {
    Assert::assertEquals(
        $expected,
        $response->getStatusCode(),
        sprintf(
            "Status %s does not match %s\n Reason: %s",
            $response->getStatusCode(),
            $expected,
            $response->getReasonPhrase()
        )
    );
  }
}
