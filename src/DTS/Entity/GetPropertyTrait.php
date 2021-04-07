<?php declare(strict_types=1);


namespace HomeCEU\DTS\Entity;


trait GetPropertyTrait {
  public function get(string $parameter) {
    return $this->{$parameter} ?? null;
  }
}
