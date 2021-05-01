<?php declare(strict_types=1);


namespace HomeCEU\DTS\Repository;


use HomeCEU\DTS\Entity\HotRenderRequest;
use HomeCEU\DTS\Persistence;

class HotRenderRepository {
  /** @var Persistence\HotRenderPersistence */
  private $persistence;

  public function __construct(Persistence $persistence) {
    $this->persistence = $persistence;
  }

  public function newHotRenderRequest(string $template, array $data): HotRenderRequest {
    return HotRenderRequest::fromState([
      'id' => $this->persistence->generateId(),
      'template' => $template,
      'data' => $data,
      'createdAt' => new \DateTime()
    ]);
  }

  public function getById(string $id): HotRenderRequest {
    $hr = $this->persistence->retrieve($id);
    return HotRenderRequest::fromState($hr);
  }

  public function save(HotRenderRequest $hotRender) {
    $this->persistence->persist($hotRender->toArray());
  }
}
