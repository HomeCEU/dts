<?php
namespace HomeCEU\DTS\Api;

class Route {
  public array $methods;
  public string $uri;
  public string $function;

  public function __construct(array $methods, string $uri, string $function) {
    $this->methods = $methods;
    $this->uri = $uri;
    $this->function = $function;
  }
}
