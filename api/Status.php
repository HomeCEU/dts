<?php declare(strict_types=1);


namespace HomeCEU\DTS\Api;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Status {
  public function __invoke(Request $request, Response $response, $args) {
    return $response->withStatus(200)->getBody()->write('OK');
  }
}
