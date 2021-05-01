<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS;


use HomeCEU\DTS\Entity\PartialBuilder;
use HomeCEU\DTS\Render\PartialInterface;

trait PartialTestTrait {
  protected function createSamplePartial(string $docType, string $key): PartialInterface {
    return PartialBuilder::create()
        ->withMetadata(['type' => 'standard'])
        ->withKey($key)
        ->withDocType($docType)
        ->withAuthor('Test Author')
        ->withBody('body')
        ->build();
  }
}
