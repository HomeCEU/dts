<?php declare(strict_types=1);


namespace HomeCEU\DTS\Repository;


use HomeCEU\DTS\Entity\Partial;
use HomeCEU\DTS\Persistence;

class PartialRepository {
  private Persistence $persistence;

  public function __construct(Persistence $persistence) {
    $this->persistence = $persistence;
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
    return array_map(function (array $p) {
      return Partial::fromState($p);
    }, $this->persistence->find(['docType' => $docType]));
  }
}
