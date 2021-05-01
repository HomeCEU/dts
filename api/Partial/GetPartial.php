<?php declare(strict_types=1);


namespace HomeCEU\DTS\Api\Partial;


use HomeCEU\DTS\Persistence\PartialPersistence;
use HomeCEU\DTS\Repository\PartialRepository;
use HomeCEU\DTS\Repository\RecordNotFoundException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class GetPartial {
  private PartialRepository $repo;

  public function __construct(ContainerInterface $container) {
    $conn = $container->get('dbConnection');
    $this->repo = new PartialRepository(new PartialPersistence($conn));
  }

  public function __invoke(Request $request, Response $response, array $args): ResponseInterface {
    try {
      $partial = $this->repo->getById($args['partialId']);
      $response->getBody()->write($partial->body);
      return $response->withStatus(200);
    } catch (RecordNotFoundException $e) {
      return $response->withStatus(404);
    }
  }
}
