<?php


namespace HomeCEU\DTS\UseCase;


use HomeCEU\DTS\Repository\DocDataRepository;

class DocDataVersionList {

  /** @var DocDataRepository */
  private $repo;

  public function __construct(DocDataRepository $repo) {
    $this->repo = $repo;
  }

  public function versions(string $docType, string $key) {
    return $this->repo->allVersions($docType, $key);
  }
}
