<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\Render;


class DefaultHelpersTest extends TestCase {
  public function testEqualHelper(): void {
    $cTemplate = $this->compile("{{#if (eq value_1 value_2)}}matches{{else}}doesn't match{{/if}}");

    $text = $this->renderHTML($cTemplate, ['value_1' => 'live', 'value_2' => 'text']);
    $this->assertEquals("doesn't match", $text);

    $text = $this->renderHTML($cTemplate, ['value_1' => 'live', 'value_2' => 'live']);
    $this->assertEquals('matches', $text);
  }
}
