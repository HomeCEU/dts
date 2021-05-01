<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\UseCase;


use Generator;
use HomeCEU\DTS\UseCase\Exception\InvalidRequestException;
use HomeCEU\DTS\UseCase\GetPartialRequest;
use PHPUnit\Framework\TestCase;

class GetPartialRequestTest extends TestCase {
  /** @dataProvider validStates() */
  public function testValidStates(array $state): void {
    $r = GetPartialRequest::fromState($state);
    $this->assertInstanceOf(GetPartialRequest::class, $r);
  }

  public function validStates(): Generator {
    yield [['id' => '1234']];
    yield [['id' => '1234', 'name' => 'a_name']];
    yield [['id' => '1234', 'docType' => 'dt']];
    yield [['name' => 'a_name', 'docType' => 'dt']];
  }

  /** @dataProvider invalidStates() */
  public function testInvalidStates(array $state): void {
    $this->expectException(InvalidRequestException::class);
    GetPartialRequest::fromState($state);
  }

  public function invalidStates(): Generator {
    yield [[]];
    yield [['name' => 'a_name']];
    yield [['docType' => 'dt']];
  }
}
