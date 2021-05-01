<?php


namespace HomeCEU\DTS\Render;


interface RenderInterface {
  public function render(string $compiledTemplate, array $data = []): string;

  public function getContentType(): string;
}
