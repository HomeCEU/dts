<?php declare(strict_types=1);


namespace HomeCEU\DTS\Render;


abstract class Render implements RenderInterface {
  protected function createTmpFileName(string $prefix): string {
    return tempnam(sys_get_temp_dir(), $prefix);
  }

  protected function renderToString(string $compiledTemplate, array $data = []): string {
    $file = $this->saveToTempFile($compiledTemplate);
    $fn = include($file);

    unlink($file);
    return trim($fn($data));
  }

  private function saveToTempFile(string $compiledTemplate): string {
    $fileName = $this->createTmpFileName('compiled_');
    file_put_contents($fileName, "<?php {$compiledTemplate}");
    return $fileName;
  }
}
