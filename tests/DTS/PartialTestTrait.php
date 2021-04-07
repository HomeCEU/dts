<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS;


use HomeCEU\DTS\Entity\PartialBuilder;
use HomeCEU\DTS\Render\PartialInterface;

trait PartialTestTrait {
  protected function createSamplePartial(string $docType, string $name): PartialInterface {
    return PartialBuilder::create()
        ->withMetadata(['type' => 'standard'])
        ->withName($name)
        ->withDocType($docType)
        ->withAuthor('Test Author')
        ->withBody('body')
        ->build();
  }
}
