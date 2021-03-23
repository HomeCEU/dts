<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\UseCase\Render;


use HomeCEU\DTS\UseCase\Exception\InvalidRequestException;
use HomeCEU\DTS\UseCase\Render\AddHotRenderRequest;
use PHPUnit\Framework\TestCase;

class AddHotRenderRequestTest extends TestCase {
  /**
   * @dataProvider validStates()
   */
  public function testValidStates(array $state): void {
    $req = AddHotRenderRequest::fromState($state);
    $this->assertInstanceOf(AddHotRenderRequest::class, $req);
  }

  public function validStates(): \Generator {
    yield [['template' => '{{ name }}', 'data' => ['name' => 'User']]];
    yield [['template' => '{{ name }}', 'data' => ['name' => 'User'], 'docType' => 'DT']];
  }

  /**
   * @dataProvider invalidStates()
   */
  public function testInvalidStates(array $state): void {
    $this->expectException(InvalidRequestException::class);
    AddHotRenderRequest::fromState($state);
  }

  public function invalidStates(): \Generator {
    yield [['docType' => 'DT']];
    yield [['data' => ['name' => 'User'], 'docType' => 'DT']];
    yield [['template' => '{{ name }}', 'docType' => 'DT']];
  }
}
