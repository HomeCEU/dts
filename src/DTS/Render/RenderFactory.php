<?php declare(strict_types=1);


namespace HomeCEU\DTS\Render;


class RenderFactory {
  public static function createHTML(): RenderInterface {
    return RenderHTML::create();
  }

  public static function createPDF(): RenderInterface {
    return RenderPDF::create();
  }
}
