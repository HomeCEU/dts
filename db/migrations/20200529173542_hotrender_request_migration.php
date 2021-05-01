<?php

use Phinx\Migration\AbstractMigration;

class HotrenderRequestMigration extends AbstractMigration {
  public function up() {
    $file = __DIR__ . '/../sql/table_hotrender_request.sql';
    $this->execute(file_get_contents($file));
  }

  public function down() {
    $this->table('hotrender_request')->drop()->save();
  }
}
