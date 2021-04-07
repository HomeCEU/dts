<?php declare(strict_types=1);


namespace HomeCEU\DTS\Repository;


use HomeCEU\DTS\Entity\Partial;
use HomeCEU\DTS\Persistence;

class PartialRepository {
  private Persistence $persistence;
  private RepoHelper $repoHelper;

  public function __construct(Persistence $persistence) {
    $this->persistence = $persistence;
    $this->repoHelper = new RepoHelper($persistence);
  }

  public function create(string $docType, string $key, string $author, string $body, array $metadata = []): Partial {
    return Partial::fromState([
        'id' => $this->persistence->generateId(),
        'docType' => $docType,
        'name' => $key,
        'author' => $author,
        'body' => $body,
        'metadata' => $metadata,
        'createdAt' => new \DateTime()
    ]);
  }

  public function save(Partial $partial): string {
    return $this->persistence->persist($partial->toArray());
  }

  public function getById(string $id): Partial {
    $record = $this->persistence->retrieve($id);
    return Partial::fromState($record);
  }

  public function findByDocType(string $docType): array {
    $partials = $this->persistence->find(['docType' => $docType]);

    return array_map(function ($key) use ($docType) {
      return $this->getPartialByKey($docType, $key);
    }, $this->repoHelper->extractUniqueProperty($partials, 'name'));
  }

  protected function getPartialByKey(string $docType, string $key): Partial {
    $row = $this->repoHelper->findNewest([
        'docType' => $docType,
        'name' => $key,
    ]);
    return Partial::fromState($row);
  }
}
