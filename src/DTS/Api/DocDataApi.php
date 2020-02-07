<?php


namespace HomeCEU\DTS\Api;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class DocDataApi {
  public function post(Request $request, Response $response) {
    $reqData = $request->getParsedBody();
    $jsonString = json_encode($reqData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    $response->getBody()->write($jsonString);
    return $response;
  }
}