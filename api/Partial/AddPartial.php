<?php declare(strict_types=1);


namespace HomeCEU\DTS\Api\Partial;


use HomeCEU\DTS\Persistence\PartialPersistence;
use HomeCEU\DTS\Repository\PartialRepository;
use HomeCEU\DTS\UseCase\AddPartial as AddPartialService;
use HomeCEU\DTS\UseCase\AddPartialRequest;
use HomeCEU\DTS\UseCase\Exception\InvalidRequestException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response;

class AddPartial {
  private AddPartialService $service;

  public function __construct(ContainerInterface $container) {
    $db = $container->get('dbConnection');

    $repo = new PartialRepository(new PartialPersistence($db));
    $this->service = new AddPartialService($repo);
  }

  public function __invoke(Request $request, Response $response): ResponseInterface {
    try {
      $req = AddPartialRequest::fromState($request->getParsedBody());
      $partial = $this->service->add($req);

      $res = AddPartialResponse::fromState($partial->toArray());
      $res->bodyUri = "/api/v1/partial/{$partial->id}";

      return $response->withStatus(201)
          ->withJson($res)
          ->withHeader("Location", $res->bodyUri);
    } catch (InvalidRequestException $e) {
      return $response->withStatus(400)->withJson(['message' => $e->getMessage()]);
    }
  }
}
