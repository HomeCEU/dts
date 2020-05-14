<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\UseCase;


use HomeCEU\DTS\UseCase\GetTemplateRequest;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;

class GetTemplateRequestTest extends TestCase {
  const SEARCH_TERM = 'search term';

  public function testBuildFromArray(): void {
    $state = ['type' => 'enrollment', 'key' => __FUNCTION__, 'search' => 'none'];
    $obj = GetTemplateRequest::fromState($state);

    Assert::assertEquals($state['type'], $obj->type);
    Assert::assertEquals($state['key'], $obj->key);
    Assert::assertEquals($state['search'], $obj->search);
  }

  /** @dataProvider validStates */
  public function testValidCases($state): void {
    $r = GetTemplateRequest::fromState($state);
    Assert::assertTrue($r->isValid());
  }

  public function validStates(): \Generator {
    yield [['type' => 'enrollment', 'key' => __FUNCTION__, 'search' => self::SEARCH_TERM]];
    yield [['type' => 'enrollment', 'search' => self::SEARCH_TERM]];
    yield [['type' => 'enrollment', 'key' => __FUNCTION__]];
    yield [['type' => 'enrollment']];
  }

  /** @dataProvider invalidStates */
  public function testInvalidCases($state): void
  {
    $r = GetTemplateRequest::fromState($state);
    Assert::assertFalse($r->isValid());
  }

  public function invalidStates(): \Generator {
    yield [['key' => __FUNCTION__, 'search' => self::SEARCH_TERM]];
    yield [['key' => __FUNCTION__]];
    yield [['search' => self::SEARCH_TERM]];
  }
}
