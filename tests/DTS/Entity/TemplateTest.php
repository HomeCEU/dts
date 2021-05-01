<?php


namespace HomeCEU\Tests\DTS\Entity;


use HomeCEU\DTS\Entity\Template;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;

class TemplateTest extends TestCase {
  public function testBuildFromState(): void {
    $fakeTemplateArray = $this->fakeTemplateArray();
    $template = Template::fromState($fakeTemplateArray);
    Assert::assertEquals($fakeTemplateArray, $template->toArray());
  }
}
