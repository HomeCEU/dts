<?php declare(strict_types=1);


namespace HomeCEU\DTS\Api\Partial;


use HomeCEU\DTS\Persistence\CompiledTemplatePersistence;
use HomeCEU\DTS\Persistence\PartialPersistence;
use HomeCEU\DTS\Persistence\TemplatePersistence;
use HomeCEU\DTS\Render\CompilationException;
use HomeCEU\DTS\Repository\PartialRepository;
use HomeCEU\DTS\Repository\TemplateRepository;
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
    $conn = $container->get('dbConnection');

    $this->service = new AddPartialService(
        new PartialRepository(new PartialPersistence($conn)),
        new TemplateRepository(
            new TemplatePersistence($conn),
            new CompiledTemplatePersistence($conn)
        )
    );
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
    } catch (CompilationException $e) {
      return $response->withStatus(409)->withJson([
          'message' => $e->getMessage(),
          'errors' => $e->errors
      ]);
    }
  }
}
