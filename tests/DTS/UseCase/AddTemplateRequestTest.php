<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\UseCase;


use HomeCEU\DTS\UseCase\AddTemplateRequest;
use HomeCEU\DTS\UseCase\Exception\InvalidAddTemplateRequestException;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;

class AddTemplateRequestTest extends TestCase {
  protected $req = ['docType', 'templateKey', 'author', 'body'];

  public function testBuildFromArray(): void {
    $state = [
        'docType' => 'enrollment',
        'templateKey' => __FUNCTION__,
        'author' => 'test',
        'body' => '1234'
    ];
    $obj = AddTemplateRequest::fromState($state);

    Assert::assertEquals($state['docType'], $obj->docType);
    Assert::assertEquals($state['templateKey'], $obj->templateKey);
    Assert::assertEquals($state['author'], $obj->author);
    Assert::assertEquals($state['body'], $obj->body);
  }

  /** @dataProvider invalidStates() */
  public function testInvalidStates(array $state) {
    $this->expectException(InvalidAddTemplateRequestException::class);
    AddTemplateRequest::fromState($state);
  }

  public function invalidStates(): \Generator {
    yield [['type' => 'E', 'key' => 'K', 'author' => 'A']];
    yield [['type' => 'E', 'key' => 'K', 'body' => 'B']];
    yield [['type' => 'E', 'author' => 'A', 'body' => 'B']];
    yield [['key' => 'K', 'author' => 'A', 'body' => 'B']];
    yield [[]];
  }
}
