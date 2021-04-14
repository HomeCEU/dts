<?php


namespace HomeCEU\Tests\DTS\Entity;


use HomeCEU\DTS\Entity\DocData;
use HomeCEU\Tests\Faker;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;

class DocDataTest extends TestCase {

  public $iso8601;

  protected function setUp(): void {
    parent::setUp();
    $fake = Faker::generator();
    $this->iso8601 = $fake->iso8601;
  }

  public function testBuildFromState() {
    $entityState = $this->fakeEntity();
    $entity = DocData::fromState($entityState);
    Assert::assertEquals($entityState, $entity->toArray());
  }

  public function testConvertsStringDatetime() {
    $entityState = $this->fakeEntity();
    $stateWithStringDatetime = $entityState;
    $stateWithStringDatetime['createdAt'] = $this->iso8601;
    $entity = DocData::fromState($stateWithStringDatetime);
    Assert::assertEquals($entityState, $entity->toArray());
  }

  protected function fakeEntity() {
    $fake = Faker::generator();
    return [
        'dataId'   => $fake->uuid,
        'docType'  => 'courseCompletionCertificate',
        'dataKey'  => $fake->md5,
        'createdAt'  => new \DateTime($this->iso8601),
        'data'       => [
            "firstName" => $fake->firstName,
            "lastName"  => $fake->lastName,
            "address"   => $fake->address,
            "email"     => $fake->email
        ]
    ];
  }
}
