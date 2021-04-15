<?php declare(strict_types=1);

use HomeCEU\DTS\Db;
use HomeCEU\DTS\Db\Connection;
use HomeCEU\DTS\Entity\Partial;
use HomeCEU\DTS\Entity\Template;
use HomeCEU\DTS\Persistence;
use HomeCEU\DTS\Persistence\CompiledTemplatePersistence;
use HomeCEU\DTS\Persistence\PartialPersistence;
use HomeCEU\DTS\Persistence\TemplatePersistence;
use HomeCEU\DTS\Repository\PartialRepository;
use HomeCEU\DTS\Repository\TemplateRepository;
use Phinx\Migration\AbstractMigration;

class PartialTable extends AbstractMigration {
  public function up(): void {
    $file = __DIR__ . '/../sql/table_partial.sql';
    $this->execute(file_get_contents($file));
    MigratePartials::execute();
  }

  public function down(): void {
    $this->table('partial')->drop()->save();
  }
}

class MigratePartials {
  private Connection $db;
  private Persistence $templatePersistence;
  private TemplateRepository $templateRepository;
  private PartialRepository $partialRepository;

  private function __construct() {
    $this->db = Db::connection();

    $this->templatePersistence = new TemplatePersistence($this->db);
    $this->templateRepository = new TemplateRepository(
        $this->templatePersistence,
        new CompiledTemplatePersistence($this->db)
    );
    $this->partialRepository = new PartialRepository(
        new PartialPersistence($this->db)
    );
  }

  public static function execute(): void {
    $mp = new self();
    $mp->db->beginTransaction();
    $mp->migrate();
    $mp->db->commit();
  }

  protected function migrate(): void {
    foreach ($this->fetchDocTypes() as $type) {
      $this->processType($type);
    }
  }

  protected function fetchDocTypes(): array {
    return array_column($this->templateRepository->docTypeList(), 'docType');
  }

  protected function processType($type): void {
    if ($this->typeIsPartial($type)) {
      $templates = $this->templatePersistence->find(['docType' => $type]);

      foreach ($templates as $template) {
        $template = Template::fromState($template);
        $this->saveNewPartial($template);
        $this->deleteTemplate($template);
      }
    }
  }

  protected function typeIsPartial($type): bool {
    return strpos($type, 'partial') || strpos($type, 'image');
  }

  protected function saveNewPartial(Template $template): void {
    $splitTypes = explode('/', $template->docType);
    $baseType = $splitTypes[0];
    $partialType = $splitTypes[1];

    $this->partialRepository->save(Partial::fromState([
        'id' => $template->id,
        'key' => $template->key,
        'docType' => $baseType,
        'body' => $template->body,
        'author' => $template->author,
        'metadata' => ['type' => $partialType],
        'createdAt' => $template->createdAt,
    ]));
  }

  protected function deleteTemplate(Template $template): void {
    $this->templatePersistence->delete($template->id);
  }
}
