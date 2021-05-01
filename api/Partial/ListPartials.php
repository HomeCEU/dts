<?php declare(strict_types=1);


namespace HomeCEU\DTS\Api\Partial;


use HomeCEU\DTS\Api\ResponseHelper;
use HomeCEU\DTS\Persistence\PartialPersistence;
use HomeCEU\DTS\Repository\PartialRepository;
use HomeCEU\DTS\UseCase\Exception\InvalidRequestException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class ListPartials {
  private PartialRepository $partialRepository;

  public function __construct(ContainerInterface $container) {
    $db = $container->get('dbConnection');
    $this->partialRepository = new PartialRepository(new PartialPersistence($db));
  }

  public function __invoke(Request $request, Response $response): ResponseInterface {
    try {
      $param = $request->getQueryParam('docType');

      if (empty($param)) {
        throw new InvalidRequestException('You must provide a value for the query parameter "docType"');
      }
      $res = $this->partialRepository->findByDocType($param);
      return $response->withStatus(200)
          ->withJson([
              'total' => count($res),
              'items' => array_map(function ($partial) {
                return ResponseHelper::partialDetailModel($partial);
              }, $res)
          ]);
    } catch (InvalidRequestException $e) {
      return $response->withStatus(400)->withJson(['message' => $e->getMessage()]);
    }
  }
}
