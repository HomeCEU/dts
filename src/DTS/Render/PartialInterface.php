<?php


namespace HomeCEU\DTS\Render;


use HomeCEU\DTS\Entity;

interface PartialInterface extends Entity {
  public function getName(): string;
  public function getBody(): string;
}
