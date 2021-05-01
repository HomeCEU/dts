<?php


namespace HomeCEU\DTS\UseCase;


use HomeCEU\DTS\Entity\Template;
use HomeCEU\DTS\Repository\TemplateRepository;

class ListTemplates {
  /** @var TemplateRepository */
  private $repo;

  public function __construct(TemplateRepository $repo) {
    $this->repo = $repo;
  }

  /**
   * @param string $searchString
   * @return Template[]
   */
  public function search(string $searchString) {
    return $this->repo->filterBySearchString($searchString);
  }

  /**
   * @param string $type
   * @return Template[]
   */
  public function filterByType(string $type) {
    return $this->repo->filterByType($type);
  }

  /** @return Template[] */
  public function all() {
    return $this->repo->latestVersions();
  }

  public function getDocTypes() {
    return $this->repo->docTypeList();
  }
}