<?php

use Phinx\Migration\AbstractMigration;

class PartialTable extends AbstractMigration {
  public function up(): void {
    $file = __DIR__ . '/../sql/table_partial.sql';
    $this->execute(file_get_contents($file));
  }

  public function down(): void {
    $this->table('partial')->drop()->save();
  }
}
