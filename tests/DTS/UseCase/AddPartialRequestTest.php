<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\UseCase;


use Generator;
use HomeCEU\DTS\UseCase\AddPartialRequest;
use HomeCEU\DTS\UseCase\Exception\InvalidAddPartialRequestException;
use PHPUnit\Framework\TestCase;

class AddPartialRequestTest extends TestCase {
  /** @dataProvider validStates */
  public function testValidState(array $state): void {
    $r = AddPartialRequest::fromState($state);
    $this->assertInstanceOf(AddPartialRequest::class, $r);
  }

  public function validStates(): Generator {
    yield [['docType' => 'dt', 'body' => '', 'name' => 'a_name', 'author' => 'an_author']];
    yield [['docType' => 'dt', 'body' => 'a body', 'name' => 'a_name', 'author' => 'an_author']];
  }

  /** @dataProvider invalidStates() */
  public function testInvalidStates(array $state): void {
    $this->expectException(InvalidAddPartialRequestException::class);
    AddPartialRequest::fromState($state);
  }

  public function invalidStates(): Generator {
    // missing docType
    yield [['name' => 'a_name', 'body' => 'a_body', 'author' => 'an_author']];
    // empty doctype
    yield [['docType' => '', 'name' => 'a_name', 'body' => 'a_body', 'author' => 'an_author']];
    // missing body
    yield [['docType' => 'dt', 'name' => 'a_name', 'author' => 'an_author']];
    // missing name
    yield [['docType' => 'dt', 'body' => 'a_body', 'author' => 'an_author']];
    // empty name
    yield [['docType' => 'dt', 'name' => '', 'body' => 'a_body', 'author' => 'an_author']];
    // missing author
    yield [['docType' => 'dt', 'name' => 'a_name', 'body' => 'a_body']];
    // empty author
    yield [['docType' => 'dt', 'name' => 'a_name', 'body' => 'a_body', 'author' => '']];
  }
}
