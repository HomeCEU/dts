<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\UseCase;


use HomeCEU\DTS\UseCase\GetTemplateRequest;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;

class GetTemplateRequestTest extends TestCase {
  public function testBuildFromArray(): void {
    $state = ['id' => 'TID', 'docType' => 'DT', 'key' => 'KEY'];
    $r = GetTemplateRequest::fromState($state);

    Assert::assertEquals($state['id'], $r->id);
    Assert::assertEquals($state['docType'], $r->docType);
    Assert::assertEquals($state['key'], $r->key);
  }

  /** @dataProvider validStates() */
  public function testValidCases(array $state): void {
    $r = GetTemplateRequest::fromState($state);
    Assert::assertTrue($r->isValid());
  }

  public function validStates(): \Generator {
    yield [['id' => 'TID']];
    yield [['id' => 'TID', 'docType' => 'DT', 'key' => 'KEY']];
    yield [['docType' => 'DT', 'key' => 'KEY']];
  }

  /** @dataProvider invalidStates() */
  public function testInvalidStates(array $state): void {
    $r = GetTemplateRequest::fromState($state);
    Assert::assertFalse($r->isValid());
  }

  public function invalidStates(): \Generator {
    yield [['docType' => 'DT']];
    yield [['key' => 'KEY']];
  }
}
