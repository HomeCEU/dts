<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\Entity;


use Generator;
use HomeCEU\DTS\Entity\CompiledTemplate;
use HomeCEU\DTS\Entity\IncompleteEntityException;
use PHPUnit\Framework\TestCase;

class CompiledTemplateTest extends TestCase {
  public function testValidStates(): void {
    $ct = CompiledTemplate::fromState(['templateId' => uniqid(), 'body' => '123', 'createdAt' => new \DateTime()]);
    $this->assertInstanceOf(CompiledTemplate::class, $ct);
  }

  /** @dataProvider invalidStates() */
  public function testInvalidStates(array $state): void {
    $this->expectException(IncompleteEntityException::class);
    $ct = CompiledTemplate::fromState($state);
    $this->assertInstanceOf(CompiledTemplate::class, $ct);
  }

  public function invalidStates(): Generator {
    // missing templateId
    yield [['body' => '123', 'createdAt' => new \DateTime()]];
    // missing body
    yield [['templateId' => uniqid(), 'createdAt' => new \DateTime()]];
    // missing createdAt
    yield [['templateId' => uniqid(), 'body' => '123']];
  }
}
