<?php


namespace HomeCEU\DTS\UseCase;


use HomeCEU\DTS\Entity\DocData;
use HomeCEU\DTS\Repository\DocDataRepository;

class GetDocData {
  /** @var DocDataRepository */
  private $repo;

  public function __construct(DocDataRepository $repo) {
    $this->repo = $repo;
  }

  public function getLatestVersion(string $type, string $key): DocData {
    $id = $this->repo->lookupId($type, $key);
    return $this->repo->getByDocDataId($id);
  }

  public function getById($dataId): DocData {
    return $this->repo->getByDocDataId($dataId);
  }
}