<?php


namespace HomeCEU\DTS\Api;


use Exception;
use Slim\Http\Request;
use Slim\Http\Response;

class ErrorHandler {
  private DiContainer $di;

  public function __construct(DiContainer $di) {
    $this->di = $di;
  }

  public function __invoke(Request $r, Response $response, \Throwable $exception) {
    $requestId = uniqid('REQ_');
    $this->di->logger->error($exception, [
        'Request' => [
            $requestId,
            $r->getMethod().' '.$r->getUri()->getPath(),
            $r->getQueryParams()
        ],
    ]);
    return $response
        ->withStatus(500)
        ->withHeader('Content-Type', 'text/html')
        ->write("Something unexpected went wrong!  Please report this issue referencing {$requestId}");
  }
}
