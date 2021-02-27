<?php
namespace HomeCEU\DTS\Api;

class Route {
  public $methods;
  public $uri;
  public $function;

  public function __construct(array $methods, string $uri, string $function) {
    $this->methods = $methods;
    $this->uri = $uri;
    $this->function = $function;
  }
}
