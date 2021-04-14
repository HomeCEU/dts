<?php

namespace HomeCEU\DTS\Repository;

use DateTime;
use HomeCEU\DTS\AbstractEntity;
use HomeCEU\DTS\Entity\DocData;
use HomeCEU\DTS\Persistence;

class DocDataRepository {
  /** @var Persistence */
  private $persistence;

  /** @var RepoHelper */
  private $repoHelper;

  public function __construct(Persistence $persistence) {
    $this->persistence = $persistence;
    $this->repoHelper = new RepoHelper($persistence);
  }

  public function save(DocData $docData): void {
    $this->persistence->persist($docData->toArray());
  }

  public function getByDocDataId($id): DocData {
    return DocData::fromState($this->persistence->retrieve($id));
  }

  public function newDocData(string $type, string $key, $data): DocData {
    return DocData::fromState(
        [
            'id' => $this->persistence->generateId(),
            'docType' => $type,
            'key' => $key,
            'data' => $data,
            'createdAt' => new \DateTime()
        ]
    );
  }

  public function allVersions(string $docType, string $key): ?array {
    $filter = [
        'docType' => $docType,
        'key' => $key
    ];
    $cols = [
        'id', 'docType', 'key', 'createdAt'
    ];
    $rows = $this->persistence->find($filter, $cols);
    return $this->toDocDataArray($rows);
  }

  public function lookupId(string $docType, string $key): string {
    $filter = [
        'docType' => $docType,
        'key' => $key
    ];
    $cols = [
        'id',
        'createdAt'
    ];
    $row = $this->repoHelper->findNewest($filter, $cols);
    return $row['id'];
  }

  private function toDocDataArray(array $rows): array {
    return array_map(function ($row) {
      return DocData::fromState($row);
    }, $rows);
  }
}
