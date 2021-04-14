<?php


namespace HomeCEU\Tests\DTS\UseCase\Render;


use HomeCEU\DTS\UseCase\Render\RenderRequest;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;

class RenderRequestTest extends TestCase {

  public function testBuildFromArray() {
    $state = ['templateId'=>'T', 'id'=>'D', 'key'=>'DK', 'docType'=>'DT', 'format' => 'PDF'];
    $object = RenderRequest::fromState($state);
    Assert::assertEquals($state['templateId'], $object->templateId);
    Assert::assertEquals($state['id'], $object->id);
    Assert::assertEquals($state['key'], $object->key);
    Assert::assertEquals($state['docType'], $object->docType);
    Assert::assertEquals($state['format'], $object->format);
  }

  public function testValidCases() {
    foreach ($this->validStates() as $state) {
      $r = RenderRequest::fromState($state);
      $msg = "state should be valid:\n".json_encode($state);
      Assert::assertTrue($r->isValid(), $msg);
    }
  }

  public function testInvalidCases() {
    foreach ($this->invalidStates() as $state) {
      $r = RenderRequest::fromState($state);
      $msg = "state should not be valid:\n".json_encode($state);
      Assert::assertFalse($r->isValid(), $msg);
    }
  }

  protected function validStates() {
    return [
        [
            'templateId' => 'T',
            'id' => 'D'
        ],
        [
            'templateId' => 'T',
            'key' => 'DK',
            'docType' => 'DT'
        ],
        [
            'id' => 'D',
            'templateKey' => 'TK',
            'docType' => 'DT'
        ],
        [
            'templateKey' => 'TK',
            'key' => 'DK',
            'docType' => 'DT'
        ]
    ];
  }

  protected function invalidStates() {
    return [
        [
            'templateId' => 'T',
            'docType' => 'D'
        ],
        [
            'key' => 'DK',
            'docType' => 'DT'
        ],
        [
            'templateKey' => 'TK',
            'id' => 'D'
        ],
        [
            'key' => 'DK',
            'templateId' => 'T'
        ],
        [
            'id' => 'D',
            'docType' => 'DT'
        ]
    ];
  }
}
