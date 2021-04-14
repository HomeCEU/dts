<?php


namespace HomeCEU\DTS\Api\DocData;


use HomeCEU\DTS\Persistence;
use HomeCEU\DTS\Persistence\DocDataPersistence;
use HomeCEU\DTS\Repository\DocDataRepository;
use HomeCEU\DTS\UseCase\AddDocData;
use HomeCEU\DTS\UseCase\Exception\InvalidDocDataAddRequestException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response;


class DocDataAdd {
  private Persistence $persistence;
  private DocDataRepository $repository;
  private AddDocData $useCase;

  public function __construct(ContainerInterface $container) {
    $this->persistence = new DocDataPersistence($container->get('dbConnection'));
    $this->repository = new DocDataRepository($this->persistence);
    $this->useCase = new AddDocData($this->repository);
  }

  public function __invoke(Request $request, Response $response, $args) {
    try {
      $reqData = $request->getParsedBody();
      $docData = $this->useCase->add(
          $reqData['docType'],
          $reqData['key'],
          $reqData['data']
      );
      $savedDocData = $this->persistence->retrieve(
          $docData['id'],
          [
              'id',
              'key',
              'docType',
              'createdAt'
          ]
      );
      $savedDocData['bodyUri'] = '/api/v1/docdata/' . $docData['id'];
      return $response->withStatus(201)->withJson($savedDocData);
    } catch (InvalidDocDataAddRequestException $e) {
      return $response->withStatus(400)->withJson(
          [
              'status' => 400,
              'errors' => [$e->getMessage()],
              'date' => new \DateTime(),
          ]
      );
    }
  }
}
