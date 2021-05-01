<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase\Render;


use HomeCEU\DTS\Render\RenderFactory;
use HomeCEU\DTS\Render\RenderInterface;

trait RenderServiceTrait {
  protected function getRenderService(string $format = null): RenderInterface {
    return ($format === RenderFormat::FORMAT_PDF)
        ? RenderFactory::createPDF()
        : RenderFactory::createHTML();
  }
}
