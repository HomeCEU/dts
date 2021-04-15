<?php


namespace HomeCEU\Tests\DTS\UseCase;

use HomeCEU\DTS\Db;
use HomeCEU\DTS\Persistence;
use HomeCEU\DTS\Persistence\TemplatePersistence;
use HomeCEU\DTS\Repository\TemplateRepository;
use HomeCEU\DTS\UseCase\ListTemplates;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;

class ListTemplatesTest extends TestCase {
  private Db\Connection $db;
  private Persistence $p;
  private ListTemplates $useCase;

  protected function setUp(): void {
    parent::setUp();
    $this->db = Db::connection();
    $this->db->beginTransaction();
    $this->p = new TemplatePersistence($this->db);
    $repo = new TemplateRepository($this->p, $this->compiledTemplatePersistence());
    $this->useCase = new ListTemplates($repo);
  }

  protected function tearDown(): void {
    parent::tearDown();
    $this->db->rollBack();
  }

  public function testListTemplatesByType() {
    $type = 'findMe';
    $this->p->persist($this->fakeTemplateArray($type, 'foo'));
    $this->p->persist($this->fakeTemplateArray($type, 'bar'));
    $this->p->persist($this->fakeTemplateArray('dontFindMe', 'baz'));
    $results = $this->useCase->filterByType($type);
    Assert::assertCount(2, $results);
  }

  public function testListAllTemplates() {
    $count = rand(20,50);
    $ids = [];
    for ($i=0;$i<$count;$i++) {
      $data = $this->fakeTemplateArray(__FUNCTION__);
      $this->p->persist($data);
      array_push($ids, $data['id']);
    }
    $resultIds = [];
    $templates = $this->useCase->all();
    foreach ($templates as $t) {
      array_push($resultIds, $t->id);
    }
    foreach ($ids as $id) {
      Assert::assertContains($id, $resultIds);
    }
  }

  public function testListTemplatesBySearchString() {
    $count = 3;
    $searchString = "cert not cool bob";

    // matching templates
    for ($i=1; $i<=$count; $i++) {
      $this->p->persist(array_merge(
          $this->fakeTemplateArray(),
          [
              'docType' => 'cert',
              'key' => 'to key or not to key '.$i,
              'name' => 'some cool name',
              'author' => 'uncle bob'
          ]
      ));
    }
    // non matching template
    $this->p->persist($this->fakeTemplateArray());
    $templates = $this->useCase->search($searchString);
    Assert::assertCount($count, $templates);
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
