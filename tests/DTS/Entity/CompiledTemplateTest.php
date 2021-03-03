<?php declare(strict_types=1);


namespace DTS\Entity;


use HomeCEU\DTS\Entity\CompiledTemplate;
use HomeCEU\Tests\DTS\TestCase;
use HomeCEU\Tests\Faker;
use PHPUnit\Framework\Assert;
use Ramsey\Uuid\Uuid;

class CompiledTemplateTest extends TestCase {
  public $iso8601;

  protected function setUp(): void {
    parent::setUp();
    $fake = Faker::generator();
    $this->iso8601 = $fake->iso8601;
  }

  public function testBuildFromState(): void {
      $state = [
          'templateId' => Uuid::uuid1()->toString(),
          'body' => '<?php /* compiled template */ ?>',
          'createdAt' => new \DateTime($this->iso8601)
      ];
      $ct = CompiledTemplate::fromState($state);
      Assert::assertEquals($state, $ct->toArray());
  }
}
