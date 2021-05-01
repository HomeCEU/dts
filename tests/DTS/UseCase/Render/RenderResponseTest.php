<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\UseCase\Render;


use HomeCEU\DTS\UseCase\Render\RenderFormat;
use HomeCEU\DTS\UseCase\Render\RenderResponse;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;

class RenderResponseTest extends TestCase {
  public function testBuildFromArray(): void {
    $state = ['path' => 'something.html', 'contentType' => RenderFormat::FORMAT_HTML];
    $obj = RenderResponse::fromState($state);
    Assert::assertEquals($state['path'], $obj->path);
    Assert::assertEquals($state['contentType'], $obj->contentType);
  }
}
