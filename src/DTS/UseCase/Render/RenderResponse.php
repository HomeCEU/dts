<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase\Render;


use HomeCEU\DTS\AbstractEntity;

class RenderResponse extends AbstractEntity {
  public string $path;
  public string $contentType;
}
