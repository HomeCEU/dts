<?php declare(strict_types=1);


namespace HomeCEU\DTS\Repository;


use HomeCEU\DTS\Entity\Partial;
use HomeCEU\DTS\Persistence;
use HomeCEU\DTS\Render\PartialInterface;

class PartialRepository {
  private Persistence $persistence;
  private RepoHelper $repoHelper;

  public function __construct(Persistence $persistence) {
    $this->persistence = $persistence;
    $this->repoHelper = new RepoHelper($persistence);
  }

  public function save(PartialInterface $partial): string {
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

  protected function getPartialByKey(string $docType, string $key): PartialInterface {
    $row = $this->repoHelper->findNewest([
        'docType' => $docType,
        'name' => $key,
    ]);
    return Partial::fromState($row);
  }
}
