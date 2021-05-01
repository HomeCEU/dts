<?php


namespace HomeCEU\DTS\Api;


use Psr\Container\ContainerInterface;
use Slim\Container;

class DiContainer extends Container implements ContainerInterface {
  public function __construct(array $values = []) {
    $values = array_merge($values, include __DIR__."/services.php");
    parent::__construct($this->loadEnvironmentalServices($values));
  }

  protected function loadEnvironmentalServices(array $values): array {
    $env = strtolower(getenv('APP_ENV'));
    if (is_file(__DIR__ . "/services.{$env}.php")) {
      $values = array_merge($values, include __DIR__ . "/services.{$env}.php");
    }
    return $values;
  }
}
