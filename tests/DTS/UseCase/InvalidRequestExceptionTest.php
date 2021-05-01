<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\UseCase;


use HomeCEU\DTS\UseCase\Exception\InvalidRequestException;
use PHPUnit\Framework\TestCase;

class InvalidRequestExceptionTest extends TestCase {
  public function testHasRequiredParameters(): void {
    try {
      $keys = ['a', 'b', 'c', 'd'];
      throw new InvalidRequestException('', $keys);
    } catch (InvalidRequestException $e) {
      $this->assertInstanceOf(InvalidRequestException::class, $e);
      $this->assertStringContainsString('a, b, c, d', $e->getMessage());
    }
  }
}
