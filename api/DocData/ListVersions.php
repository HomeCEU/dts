<?php


namespace HomeCEU\DTS\Api\DocData;


use HomeCEU\DTS\Api\ResponseHelper;
use HomeCEU\DTS\Entity\DocData;
use HomeCEU\DTS\Persistence;
use HomeCEU\DTS\Persistence\DocDataPersistence;
use HomeCEU\DTS\Repository\DocDataRepository;
use HomeCEU\DTS\UseCase\DocDataVersionList;
use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ListVersions {
  private Persistence $persistence;
  private DocDataRepository $repository;
  private DocDataVersionList $useCase;
  private ContainerInterface $di;

  public function __construct(ContainerInterface $diContainer) {
    $this->di = $diContainer;
    $this->persistence = new DocDataPersistence($this->di->dbConnection);
    $this->repository = new DocDataRepository($this->persistence);
    $this->useCase = new DocDataVersionList($this->repository);
  }

  public function __invoke(Request $request, Response $response, $args) {
      $versions = $this->useCase->versions($args['docType'], $args['key']);
      $responseData = [
          'total' => count($versions),
          'items' => array_map(function (DocData $d) {
            return ResponseHelper::docDataDetailModel($d);
          }, $versions)
      ];
      return $response->withJson($responseData);
  }
}
