<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\Entity;


use HomeCEU\DTS\Entity\HotRenderRequest;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;

class HotRenderRequestTest extends TestCase {
  public function testBuildFromState(): void {
    $state = [
        'id' => 'RID',
        'template' => 'Hi {{ name }}',
        'data' => ['name' => 'test'],
        'createdAt' => new \DateTime('yesterday')
    ];
    $r = HotRenderRequest::fromState($state);
    Assert::assertEquals($state['id'], $r->id);
    Assert::assertEquals($state['template'], $r->template);
    Assert::assertEquals($state['data'], $r->data);
    Assert::assertEquals($state['createdAt'], $r->createdAt);
  }
}
