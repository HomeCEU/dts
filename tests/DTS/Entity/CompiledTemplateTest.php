<?php declare(strict_types=1);


namespace DTS\Entity;


use HomeCEU\DTS\Entity\CompiledTemplate;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;
use Ramsey\Uuid\Uuid;

class CompiledTemplateTest extends TestCase {
  public function testBuildFromState(): void {
      $state = [
          'templateId' => Uuid::uuid1()->toString(),
          'body' => '<?php /* compiled template */ ?>',
          'createdAt' => new \DateTime()
      ];
      $ct = CompiledTemplate::fromState($state);
      Assert::assertEquals($state, $ct->toArray());
  }
}
