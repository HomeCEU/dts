<?php


namespace HomeCEU\DTS\Repository;


use DateTime;
use HomeCEU\DTS\Entity\CompiledTemplate;
use HomeCEU\DTS\Entity\Template;
use HomeCEU\DTS\Persistence;
use HomeCEU\DTS\Persistence\TemplatePersistence;
use HomeCEU\DTS\Render\Image;
use HomeCEU\DTS\Render\Partial;
use Nette\Database\ForeignKeyConstraintViolationException;

class TemplateRepository {
  private Persistence $persistence;
  private Persistence $compiledTemplatePersistence;
  private RepoHelper $repoHelper;

  public function __construct(
      Persistence $persistence,
      Persistence $compiledTemplatePersistence
  ) {
    $this->persistence = $persistence;
    $this->compiledTemplatePersistence = $compiledTemplatePersistence;

    $this->repoHelper = new RepoHelper($persistence);
  }

  public function createNewTemplate(string $docType, string $key, string $author, string $body): Template {
    $x = 1;
    return Template::fromState([
        'templateId' => $this->persistence->generateId(),
        'docType' => $docType,
        'templateKey' => $key,
        'author' => $author,
        'body' => $body,
        'createdAt' => (new DateTime())->format(DateTime::ISO8601),
    ]);
  }

  public function createNewCompiledTemplate(Template $template, string $compiled): CompiledTemplate {
    return CompiledTemplate::fromState([
        'templateId' => $template->templateId,
        'body' => $compiled,
        'createdAt' => (new DateTime())->format(DateTime::ISO8601),
    ]);
  }

  public function save(Template $template): void {
    $this->persistence->persist($template->toArray());
  }

  public function getTemplateById(string $id): Template {
    $array = $this->persistence->retrieve($id);
    return Template::fromState($array);
  }

  public function addCompiled(Template $template, string $compiled): void {
    try {
      $this->compiledTemplatePersistence->persist(CompiledTemplate::fromState([
          'templateId' => $template->templateId,
          'body' => $compiled,
          'createdAt' => (new DateTime())->format(DateTime::ISO8601),
      ])->toArray());
    } catch (ForeignKeyConstraintViolationException $e) {
      throw new RecordNotFoundException("Cannot add compiled template, template not found {$template->templateId}");
    }
  }

  public function getCompiledTemplateById(string $id): CompiledTemplate {
    $arr = $this->compiledTemplatePersistence->retrieve($id);
    return CompiledTemplate::fromState($arr);
  }

  public function findByDocType(string $docType): array {
    $templates = $this->persistence->find(['docType' => $docType]);

    return array_map(function ($key) use ($docType) {
      return $this->getTemplateByKey($docType, $key);
    }, $this->repoHelper->extractUniqueProperty($templates, 'templateKey'));
  }

  public function findPartialsByDocType(string $docType): array {
    return array_map(function ($partial) {
      return new Partial($partial->templateKey, $partial->body);
    }, $this->findByDocType($docType . '/partial'));
  }

  public function findImagesByDocType(string $docType): array {
    return array_map(function ($partial) {
      return new Image($partial->templateKey, $partial->body);
    }, $this->findByDocType($docType . '/image'));
  }

  public function getTemplateByKey(string $docType, string $key): Template {
    $filter = [
        'docType' => $docType,
        'templateKey' => $key
    ];
    $row = $this->repoHelper->findNewest($filter);
    return Template::fromState($row);
  }

  public function lookupId($docType, $templateKey): string {
    $filter = [
        'docType' => $docType,
        'templateKey' => $templateKey
    ];
    $cols = [
        'templateId',
        'createdAt'
    ];
    $row = $this->repoHelper->findNewest($filter, $cols);
    return $row['templateId'];
  }

  /** @return Template[] */
  public function latestVersions() {
    $cols = ['templateId','docType','templateKey','name','author','createdAt'];
    $rows = $this->persistence->latestVersions($cols);
    return $this->toTemplateArray($rows);
  }

  /**
   * @param $type
   * @return Template[]
   */
  public function filterByType($type) {
    $cols = ['templateId','docType','templateKey','name','author','createdAt'];
    $rows = $this->persistence->filterByDoctype($type, $cols);
    return $this->toTemplateArray($rows);
  }

  /**
   * @param $searchString
   * @return Template[]
   */
  public function filterBySearchString($searchString) {
    $cols = ['templateId','docType','templateKey','name','author','createdAt'];
    $rows = $this->persistence->filterBySearchString($searchString, $cols);
    return $this->toTemplateArray($rows);
  }

  /**
   * @param string $type
   * @param string $key
   * @return Template[]
   */
  public function getVersions(string $type, string $key) {
    $filter = [
        'docType' => $type,
        'templateKey' => $key
    ];
    $cols = ['templateId','docType','templateKey','name','author','createdAt'];
    $rows = $this->persistence->find($filter, $cols);
    return $this->toTemplateArray($rows);
  }

  public function docTypeList(): array {
    return $this->persistence->listDocTypes();
  }

  /**
   * @param array $rows
   * @return array|Template[]
   */
  private function toTemplateArray(array $rows) {
    return array_map(function ($row) {
      return Template::fromState($row);
    }, $rows);
  }
}
