<?php
namespace HomeCEU\Tests\DTS;

use DateTime;
use HomeCEU\DTS\Entity\Template;
use HomeCEU\DTS\Persistence;

class TestCase extends \HomeCEU\Tests\TestCase {

  protected function fakeTemplate($docType = null, $key = null): Template {
    return Template::fromState($this->fakeTemplateArray($docType, $key));
  }

  protected function fakeCompiledTemplate(array $template): array {
    return [
        'templateId' => $template['templateId'],
        'body' => 'a template body',
        'createdAt' => new DateTime('yesterday'),
    ];
  }

  protected function fakePersistence($table, $idCol) {
    return new class($table, $idCol) extends Persistence\InMemory {
      private $table;
      private $idCol;

      public function __construct($table, $idCol) {
        $this->table = $table;
        $this->idCol = $idCol;
      }

      public function getTable() {
        return $this->table;
      }

      public function idColumns(): array {
        return [$this->idCol];
      }
    };
  }

  protected function uniqueName($prefix, $substring) {
    return implode('-',[$prefix, $substring, uniqid()]);
  }
}
