<?php


namespace HomeCEU\Tests\DTS\UseCase\Render;


use HomeCEU\DTS\UseCase\Render\RenderRequest;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;

class RenderRequestTest extends TestCase {

  public function testBuildFromArray() {
    $state = ['templateId'=>'T', 'dataId'=>'D', 'dataKey'=>'DK', 'docType'=>'DT', 'format' => 'PDF'];
    $object = RenderRequest::fromState($state);
    Assert::assertEquals($state['templateId'], $object->templateId);
    Assert::assertEquals($state['dataId'], $object->dataId);
    Assert::assertEquals($state['dataKey'], $object->dataKey);
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
            'dataId' => 'D'
        ],
        [
            'templateId' => 'T',
            'dataKey' => 'DK',
            'docType' => 'DT'
        ],
        [
            'dataId' => 'D',
            'templateKey' => 'TK',
            'docType' => 'DT'
        ],
        [
            'templateKey' => 'TK',
            'dataKey' => 'DK',
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
            'dataKey' => 'DK',
            'docType' => 'DT'
        ],
        [
            'templateKey' => 'TK',
            'dataId' => 'D'
        ],
        [
            'dataKey' => 'DK',
            'templateId' => 'T'
        ],
        [
            'dataId' => 'D',
            'docType' => 'DT'
        ]
    ];
  }
}
