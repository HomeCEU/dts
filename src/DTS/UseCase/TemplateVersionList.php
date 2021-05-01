<?php


namespace HomeCEU\DTS\UseCase;


use HomeCEU\DTS\Entity\Template;
use HomeCEU\DTS\Repository\TemplateRepository;

class TemplateVersionList {
  /** @var TemplateRepository */
  private $repo;

  public function __construct(TemplateRepository $repo) {
    $this->repo = $repo;
  }

  /**
   * @param string $docType
   * @param string $key
   * @return Template[]
   */
  public function getVersions(string $docType, string $key) {
    return $this->repo->getVersions($docType, $key);
  }
}
