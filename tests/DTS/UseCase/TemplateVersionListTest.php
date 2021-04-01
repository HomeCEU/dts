<?php


namespace HomeCEU\Tests\DTS\UseCase;


use HomeCEU\DTS\Db;
use HomeCEU\DTS\Entity\Template;
use HomeCEU\DTS\Persistence;
use HomeCEU\DTS\Persistence\TemplatePersistence;
use HomeCEU\DTS\Repository\TemplateRepository;
use HomeCEU\DTS\UseCase\TemplateVersionList;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;

class TemplateVersionListTest extends TestCase {
  private Db\Connection $db;
  private TemplatePersistence $p;
  private TemplateVersionList $useCase;
  private string $docType;

  protected function setUp(): void {
    parent::setUp();
    $this->db = Db::connection();
    $this->db->beginTransaction();
    $this->p = new TemplatePersistence($this->db);
    $repo = new TemplateRepository($this->p, $this->compiledTemplatePersistence());
    $this->useCase = new TemplateVersionList($repo);
  }

  protected function tearDown(): void {
    parent::tearDown();
    $this->db->rollBack();
  }

  public function testGetVersions(): void {
    // fixture data
    $key1 = self::faker()->colorName;
    $key2 = self::faker()->monthName;
    $ids = [
        $key1 => [],
        $key2 => []
    ];
    // create 10 to 20 certs between 2 different keys
    for ($i=0; $i<=rand(10,20); $i++) {
      $key = rand(0,1) == 0 ? $key1 : $key2;
      $id = $this->newPersistedTemplateVersion($key);
      $ids[$key][] = $id;
    }

    // ensure we get the correct templateIds for each key
    foreach ([$key1, $key2] as $k) {
      $results = $this->useCase->getVersions($this->docType, $k);
      Assert::assertSameSize($ids[$k], $results);
      foreach ($results as $t) {
        Assert::assertContains($t->templateId, $ids[$k]);
      }
    }
  }



  protected function newPersistedTemplateVersion($key): string {
    if (empty($this->docType)) {
      $this->docType = uniqid('TemplateVersionListTest-');
    }
    $t = $this->newTemplate([
        'docType' => $this->docType,
        'templateKey' => $key
    ]);
    $this->p->persist($t->toArray());
    return $t->templateId;
  }

  protected function compiledTemplatePersistence(): Persistence {
    return new class extends Persistence\InMemory {

      public function getTable(): string {
        return Persistence\CompiledTemplatePersistence::TABLE;
      }

      public function idColumns(): array {
        return [ Persistence\CompiledTemplatePersistence::ID_COL ];
      }
    };
  }
}
