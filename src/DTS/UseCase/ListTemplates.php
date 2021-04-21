<?php


namespace HomeCEU\DTS\UseCase;


use HomeCEU\DTS\Entity\Template;
use HomeCEU\DTS\Repository\TemplateRepository;
use Nette\Caching\Cache;

class ListTemplates {
  private TemplateRepository $repo;
  private Cache $cache;

  public function __construct(TemplateRepository $repo, Cache $cache) {
    $this->repo = $repo;
    $this->cache = $cache;
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
