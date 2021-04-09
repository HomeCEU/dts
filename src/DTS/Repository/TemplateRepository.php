<?php declare(strict_types=1);


namespace HomeCEU\DTS\Repository;


use DateTime;
use HomeCEU\DTS\Entity\CompiledTemplate;
use HomeCEU\DTS\Entity\Template;
use HomeCEU\DTS\Persistence;
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

  public function saveCompiled(Template $template, string $compiled): void {
    try {
      $ct = $this->buildCompiledTemplate($template, $compiled);
      if (!$this->hasCompiledTemplateForTemplate($template)) {
        $this->compiledTemplatePersistence->persist($ct);
        return;
      }
      $this->compiledTemplatePersistence->update($ct);
    } catch (ForeignKeyConstraintViolationException $e) {
      throw new RecordNotFoundException("Cannot add compiled template, template not found {$template->templateId}");
    }
  }

  public function getCompiledTemplateById(string $id): CompiledTemplate {
    $arr = $this->compiledTemplatePersistence->retrieve($id);
    return CompiledTemplate::fromState($arr);
  }

  /** @return Template[] */
  public function findByDocType(string $docType): array {
    $templates = $this->persistence->find(['docType' => $docType]);

    return array_map(function ($key) use ($docType) {
      return $this->getTemplateByKey($docType, $key);
    }, $this->repoHelper->extractUniqueProperty($templates, 'templateKey'));
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
  public function latestVersions(): array {
    $cols = ['templateId', 'docType', 'templateKey', 'name', 'author', 'createdAt'];
    $rows = $this->persistence->latestVersions($cols);
    return $this->toTemplateArray($rows);
  }

  /** @return Template[] */
  public function filterByType($type): array {
    $cols = ['templateId', 'docType', 'templateKey', 'name', 'author', 'createdAt'];
    $rows = $this->persistence->filterByDoctype($type, $cols);
    return $this->toTemplateArray($rows);
  }

  /** @return Template[] */
  public function filterBySearchString($searchString): array {
    $cols = ['templateId', 'docType', 'templateKey', 'name', 'author', 'createdAt'];
    $rows = $this->persistence->filterBySearchString($searchString, $cols);
    return $this->toTemplateArray($rows);
  }

  /** @return Template[] */
  public function getVersions(string $type, string $key): array {
    $filter = [
        'docType' => $type,
        'templateKey' => $key
    ];
    $cols = ['templateId', 'docType', 'templateKey', 'name', 'author', 'createdAt'];
    $rows = $this->persistence->find($filter, $cols);
    return $this->toTemplateArray($rows);
  }

  public function docTypeList(): array {
    return $this->persistence->listDocTypes();
  }

  /** @return Template[] */
  private function toTemplateArray(array $rows): array {
    return array_map(function ($row) {
      return Template::fromState($row);
    }, $rows);
  }

  private function hasCompiledTemplateForTemplate(Template $template): bool {
    return !empty($this->compiledTemplatePersistence->find(['templateId' => $template->templateId]));
  }

  private function buildCompiledTemplate(Template $template, string $compiled): array {
    return CompiledTemplate::fromState([
        'templateId' => $template->templateId,
        'body' => $compiled,
        'createdAt' => (new DateTime())->format(DateTime::ISO8601),
    ])->toArray();
  }
}
