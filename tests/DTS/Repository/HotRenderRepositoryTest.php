<?php declare(strict_types=1);


namespace HomeCEU\Tests\DTS\Repository;


use HomeCEU\DTS\Db;
use HomeCEU\DTS\Entity\HotRenderRequest;
use HomeCEU\DTS\Persistence;
use HomeCEU\DTS\Persistence\HotRenderPersistence;
use HomeCEU\DTS\Repository\HotRenderRepository;
use HomeCEU\Tests\DTS\TestCase;
use PHPUnit\Framework\Assert;

class HotRenderRepositoryTest extends TestCase {
  private Persistence $persistence;
  private HotRenderRepository $repo;
  private Db\Connection $db;

  protected function setUp(): void {
    parent::setUp();
    $this->db = Db::connection();
    $this->db->beginTransaction();

    $this->persistence = new HotRenderPersistence($this->db);
    $this->repo = new HotRenderRepository($this->persistence);
  }

  protected function tearDown(): void {
    $this->db->rollBack();
    parent::tearDown();
  }

  public function testGetById(): void {
    $request = $this->fakeHotRenderRequestArray();
    $this->persistence->persist($request);

    $entity = HotRenderRequest::fromState($request);
    $hotRender = $this->repo->getById($request['id']);
    Assert::assertEquals($entity, $hotRender);
  }

  public function testSave(): void {
    $hotRender = HotRenderRequest::fromState($this->fakeHotRenderRequestArray());
    $this->repo->save($hotRender);

    Assert::assertEquals($hotRender->toArray(), $this->persistence->retrieve($hotRender->id));
  }

  protected function fakeHotRenderRequestArray(): array {
    return [
        'id' => $this->persistence->generateId(),
        'template' => '<?php /* a compiled template */ ?>',
        'data' => ['name' => 'test'],
        'createdAt' => new \DateTime('yesterday')
    ];
  }
}
