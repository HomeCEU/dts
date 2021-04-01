<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\UseCase;


use Generator;
use HomeCEU\DTS\UseCase\AddPartialRequest;
use HomeCEU\DTS\UseCase\Exception\InvalidAddPartialRequestException;
use PHPUnit\Framework\TestCase;

class AddPartialRequestTest extends TestCase {
  public function testValidState(): void {
    $state = [
        'docType' => 'dt',
        'body' => 'a_body',
        'name' => 'a_name',
        'author' => 'an_author'
    ];
    $r = AddPartialRequest::fromState($state);
    $this->assertInstanceOf(AddPartialRequest::class, $r);
  }

  /** @dataProvider invalidStates() */
  public function testInvalidStates(array $state): void {
    $this->expectException(InvalidAddPartialRequestException::class);
    AddPartialRequest::fromState($state);
  }

  public function invalidStates(): Generator {
    // empty docType
    yield [['docType' => '', 'body' => 'a_body', 'name' => 'a_name', 'author' => 'an_author']];
    // empty body
    yield [['docType' => 'dt', 'body' => '', 'name' => 'a_name', 'author' => 'an_author']];
    // empty name
    yield [['docType' => 'dt', 'body' => 'a_body', 'name' => '', 'author' => 'an_author']];
    // empty author
    yield [['docType' => 'dt', 'body' => 'a_body', 'name' => 'a_name', 'author' => '']];
  }
}
