<?php
namespace HomeCEU\DTS\Api;

class Route {
  public $methods;
  public $uri;
  public $function;

  public function __construct(string $methods, string $uri, string $function) {
    $this->methods = explode(',', str_replace(' ', '', $methods));
    $this->uri = $uri;
    $this->function = $function;
  }
}
