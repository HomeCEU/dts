<?php


namespace HomeCEU\DTS\Api\DocData;


use Exception;
use HomeCEU\DTS\Api\DiContainer;
use HomeCEU\DTS\Api\ResponseHelper;
use HomeCEU\DTS\Persistence\DocDataPersistence;
use HomeCEU\DTS\Repository\DocDataRepository;
use HomeCEU\DTS\Repository\RecordNotFoundException;
use HomeCEU\DTS\UseCase\GetDocData;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\NotFoundException;
use Slim\Http\Response;

class GetDocDataByKey {
  private GetDocData $useCase;

  public function __construct(DiContainer $di) {
    $db = $di->dbConnection;
    $repo = new DocDataRepository(new DocDataPersistence($db));
    $this->useCase = new GetDocData($repo);
  }

  public function __invoke(Request $request, Response $response, $args) {
    try {
      $entity = $this->useCase->getLatestVersion($args['docType'], $args['dataKey']);
      $responseData = ResponseHelper::docDataModel($entity);
      return $response
          ->withStatus(200)
          ->withJson($responseData);
    } catch (RecordNotFoundException $e) {
      throw new NotFoundException($request, $response);
    }
  }
}
