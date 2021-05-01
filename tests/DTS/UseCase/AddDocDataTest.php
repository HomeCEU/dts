<?php

namespace HomeCEU\Tests\DTS\UseCase;

use HomeCEU\DTS\Persistence;
use HomeCEU\DTS\Persistence\InMemory\DocDataPersistence;
use HomeCEU\DTS\Repository\DocDataRepository;
use HomeCEU\DTS\UseCase\AddDocData;
use HomeCEU\DTS\UseCase\Exception\InvalidDocDataAddRequestException;
use HomeCEU\Tests\Faker;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;

class AddDocDataTest extends TestCase {
  const ENTITY_TYPE = 'person';

  /** @var  Persistence */
  private $persistence;

  /** @var  DocDataRepository */
  private $repository;

  protected function setUp(): void {
    parent::setUp();
    $this->persistence = new DocDataPersistence();
    $this->repository = new DocDataRepository($this->persistence);
  }

  /**
   * @dataProvider invalidRequestProvider
   */
  public function testInvalidRequestThrowsException($type, $key): void {
    $this->expectException(InvalidDocDataAddRequestException::class);
    $uc = new AddDocData($this->repository);
    $uc->add($type, $key, ['data' => 'somedata']);
  }

  public function invalidRequestProvider(): \Generator {
    yield [self::ENTITY_TYPE, null];
    yield [self::ENTITY_TYPE, ''];
    yield ['', uniqid()];
    yield [null, uniqid()];
  }

  public function testCanAddEntity(): void {
    $fake = Faker::generator();
    $inputs = [
        'docType' => self::ENTITY_TYPE,
        'key' => $fake->md5,
        'data' => $this->profileData()
    ];
    $uc = new AddDocData($this->repository);
    $docData = $uc->add(
        $inputs['docType'],
        $inputs['key'],
        $inputs['data']
    );
    $savedDocData = $this->persistence->retrieve($docData['id']);
    Assert::assertEquals($docData, $savedDocData);
  }

  protected function profileData(): array {
    $fake = Faker::generator();
    return [
        "firstName" => $fake->firstName,
        "lastName" => $fake->lastName,
        "address" => $fake->address,
        "email" => $fake->email
    ];
  }
}
