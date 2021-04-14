<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS;


use HomeCEU\DTS\Entity\IdGenerator;
use HomeCEU\DTS\Entity\Template;

trait TemplateTestTrait {
  protected function createSampleTemplate(string $docType, string $key, string $body = ''): Template {
    return Template::fromState([
        'id' => IdGenerator::create(),
        'key' => $key,
        'docType' => $docType,
        'author' => 'Test Author',
        'body' => $body,
        'createdAt' => new \DateTime(),
    ]);
  }
}
