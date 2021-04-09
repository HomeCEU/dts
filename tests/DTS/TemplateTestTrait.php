<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS;


use HomeCEU\DTS\Entity\IdGenerator;
use HomeCEU\DTS\Entity\Template;

trait TemplateTestTrait {
  protected function createSampleTemplate(string $docType, string $key, string $body = ''): Template {
    return Template::fromState([
        'templateId' => IdGenerator::create(),
        'docType' => $docType,
        'templateKey' => $key,
        'author' => 'Test Author',
        'body' => $body,
        'createdAt' => new \DateTime(),
    ]);
  }
}
