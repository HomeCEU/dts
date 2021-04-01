<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\UseCase;


use HomeCEU\DTS\Persistence;
use HomeCEU\DTS\Repository\PartialRepository;
use HomeCEU\DTS\UseCase\AddPartial;
use HomeCEU\DTS\UseCase\AddPartialRequest;
use HomeCEU\Tests\DTS\TestCase;

class AddPartialTest extends TestCase {
  private Persistence $persistence;
  private AddPartial $service;

  protected function setUp(): void {
    parent::setUp();
    $this->persistence = $this->fakePersistence('partial', 'id');
    $this->service = new AddPartial(new PartialRepository($this->persistence));
  }

  public function testAddPartialFromRequest(): void {
    $state = [
        'docType' => 'dt',
        'name' => 'a_name',
        'body' => 'Here is a {{ body }}',
        'author' => 'an_author',
        'metadata' => [
          'type' => 'generic'
        ]
    ];
    $saved = $this->service->add(AddPartialRequest::fromState($state));
    $found = $this->persistence->retrieve($saved->id);
    $this->assertEquals($found, $saved->toArray());
  }
}
