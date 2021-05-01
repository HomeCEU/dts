<?php declare(strict_types=1);


namespace HomeCEU\DTS\Render;


class RenderHTML extends Render {
  public static function create(): RenderInterface {
    return new static();
  }

  public function render(string $compiledTemplate, array $data = []): string {
    $t = $this->renderToString($compiledTemplate, $data);
    $path = $this->createTmpFileName('html_');
    file_put_contents($path, $t);
    return $path;
  }

  public function getContentType(): string {
    return 'text/html';
  }
}
