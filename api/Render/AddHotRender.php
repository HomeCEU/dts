<?php declare(strict_types=1);


namespace HomeCEU\DTS\Api\Render;


use HomeCEU\DTS\Persistence\HotRenderPersistence;
use HomeCEU\DTS\Persistence\PartialPersistence;
use HomeCEU\DTS\Render\CompilationException;
use HomeCEU\DTS\Repository\HotRenderRepository;
use HomeCEU\DTS\Repository\PartialRepository;
use HomeCEU\DTS\UseCase\Exception\InvalidRequestException;
use HomeCEU\DTS\UseCase\Render\AddHotRender as AddHotRenderUseCase;
use HomeCEU\DTS\UseCase\Render\AddHotRenderRequest;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class AddHotRender {
  private AddHotRenderUseCase $useCase;

  public function __construct(ContainerInterface $container) {
    $conn = $container->get('dbConnection');

    $hotRenderRepo = new HotRenderRepository(new HotRenderPersistence($conn));
    $partialRepo = new PartialRepository(new PartialPersistence($conn));
    $this->useCase = new AddHotRenderUseCase($hotRenderRepo, $partialRepo);
  }

  public function __invoke(Request $request, Response $response): ResponseInterface {
    try {
      $reqData = $request->getParsedBody();
      $renderRequest = $this->useCase->add(
          AddHotRenderRequest::fromState([
              'template' => !empty($reqData['template']) ? $reqData['template'] : '',
              'data' => !empty($reqData['data']) ? $reqData['data'] : [],
              'docType' => !empty($reqData['docType']) ? $reqData['docType'] : '',
          ])
      );
      $route = "/api/v1/hotrender/{$renderRequest['id']}";
      return $response->withStatus(201)
          ->withHeader('Location', $route)
          ->withJson([
              'id' => $renderRequest['id'],
              'createdAt' => $renderRequest['createdAt'],
              'location' => $route
          ]);
    } catch (InvalidRequestException $e) {
      return $response->withStatus(400)->withJson([
          'status' => 400,
          'errors' => [$e->getMessage()],
          'date' => new \DateTime(),
      ]);
    } catch (CompilationException $e) {
      return $response->withStatus(409)->withJson([
          'message' => 'Cannot create hot render request',
          'errors' => [$e->getMessage()],
      ]);
    }
  }
}
