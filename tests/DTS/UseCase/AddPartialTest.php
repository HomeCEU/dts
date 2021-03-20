<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\UseCase;


use HomeCEU\DTS\Entity\Partial;
use HomeCEU\DTS\Persistence\InMemory;
use HomeCEU\DTS\UseCase\AddPartial;
use HomeCEU\DTS\UseCase\AddPartialRequest;
use HomeCEU\Tests\DTS\TestCase;

class AddPartialTest extends TestCase {
  private InMemory $persistence;
  private AddPartial $service;

  protected function setUp(): void {
    parent::setUp();
    $this->persistence = $this->fakePersistence('partial', 'partialId');
    $this->service = new AddPartial();
  }

  public function testAddPartialFromRequest(): void {
    $state = [
        'docType' => 'dt',
        'name' => 'a_name',
        'body' => 'Here is a {{ body }}',
        'author' => 'an_author'
    ];
    $request = AddPartialRequest::fromState($state);

    $partial = $this->service->newPartial($request);
    $this->assertInstanceOf(Partial::class, $partial);
    $this->assertNotEmpty($partial->get('id'));
  }
}
