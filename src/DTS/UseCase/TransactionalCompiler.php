<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase;


use HomeCEU\DTS\Db;
use HomeCEU\DTS\Db\Connection;
use HomeCEU\DTS\Entity\Template;
use HomeCEU\DTS\Render\CompilationException;
use HomeCEU\DTS\Render\PartialInterface;
use HomeCEU\DTS\Render\RenderHelper;
use HomeCEU\DTS\Render\TemplateCompiler;
use HomeCEU\DTS\Repository\PartialRepository;
use HomeCEU\DTS\Repository\TemplateRepository;

/**
 * Class DocTypeCompiler
 *
 * Compiles all Templates and Partials for a given docType
 *
 * @author Dan McAdams
 * @package HomeCEU\DTS\UseCase
 */
class TransactionalCompiler {
  private Connection $connection;
  private TemplateRepository $templateRepository;
  private PartialRepository $partialRepository;
  private TemplateCompiler $compiler;
  private array $errors;

  public function __construct(TemplateRepository $templateRepository, PartialRepository $partialRepository) {
    $this->connection = Db::connection();
    $this->templateRepository = $templateRepository;
    $this->partialRepository = $partialRepository;
    $this->compiler = TemplateCompiler::create();
  }

  public function compileAllTemplatesForDocType(string $type): void {
    $this->beginTransaction();

    $this->compiler->setPartials($this->partialRepository->findByDocType($type));
    foreach ($this->templateRepository->findByDocType($type) as $template) {
      $this->compileTemplate($template);
    }
    $this->endTransaction();
  }

  /**
   * Only compile the template if it expects the partial
   * this should save time and calls to the DB
   *
   * @param PartialInterface $partial
   */
  public function compileTemplatesForPartial(PartialInterface $partial): void {
    $this->beginTransaction();

    $type = $partial->get('docType');
    $this->compiler->setPartials($this->partialRepository->findByDocType($type));

    foreach ($this->templateRepository->findByDocType($type) as $template) {
      if ($this->templateExpectsPartial($template, $partial)) {
        $this->compileTemplate($template);
      }
    }
    $this->endTransaction();
  }

  private function compileTemplate(Template $template): void {
    try {
      $ct = $this->compiler->compile($template->body);
      $this->templateRepository->saveCompiled($template, $ct);
    } catch (CompilationException $e) {
      $this->errors[] = [
          'code' => $e->getCode(),
          'message' => $e->getMessage(),
          'template' => [
              'id' => $template->id,
              'docType' => $template->docType,
              'key' => $template->key,
              'partials' => RenderHelper::extractPartials($template->body),
          ]
      ];
    }
  }

  private function beginTransaction() {
    if (!$this->connection->inTransaction) {
      $this->connection->beginTransaction();
    }
    $this->errors = [];
  }

  private function endTransaction(): void {
    if (!empty($this->errors)) {
      $this->rollback();
      throw CompilationException::create("Error compiling templates.", 1, $this->errors);
    }
    $this->commit();
  }

  private function commit(): void {
    if ($this->connection->inTransaction) {
      $this->connection->commit();
    }
  }

  private function rollback(): void {
    if ($this->connection->inTransaction) {
      $this->connection->rollBack();
    }
  }

  private function templateExpectsPartial(Template $template, PartialInterface $partial): bool {
    $ps = RenderHelper::extractPartials($template->body);
    return in_array($partial->getKey(), RenderHelper::extractPartials($template->body));
  }
}
