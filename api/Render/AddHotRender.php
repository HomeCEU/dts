<?php declare(strict_types=1);


namespace HomeCEU\DTS\Api\Render;


use HomeCEU\DTS\Persistence\CompiledTemplatePersistence;
use HomeCEU\DTS\Persistence\HotRenderPersistence;
use HomeCEU\DTS\Persistence\TemplatePersistence;
use HomeCEU\DTS\Render\CompilationException;
use HomeCEU\DTS\Repository\HotRenderRepository;
use HomeCEU\DTS\Repository\TemplateRepository;
use HomeCEU\DTS\UseCase\Render\AddHotRender as AddHotRenderUseCase;
use HomeCEU\DTS\UseCase\Render\AddHotRenderRequest;
use HomeCEU\DTS\UseCase\Render\InvalidHotRenderRequestException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AddHotRender {
  private AddHotRenderUseCase $useCase;

  public function __construct(ContainerInterface $container) {
    $conn = $container->dbConnection;

    $templateRepo = new TemplateRepository(
        new TemplatePersistence($conn),
        new CompiledTemplatePersistence($conn)
    );
    $hotRenderRepo = new HotRenderRepository(
        new HotRenderPersistence($conn)
    );
    $this->useCase = new AddHotRenderUseCase($hotRenderRepo, $templateRepo);
  }

  public function __invoke(Request $request, Response $response): Response {
    try {
      $reqData = $request->getParsedBody();
      $renderRequest = $this->useCase->add(
          AddHotRenderRequest::fromState([
              'template' => !empty($reqData['template']) ? $reqData['template'] : '',
              'data' => !empty($reqData['data']) ? $reqData['data'] : [],
              'docType' => !empty($reqData['docType']) ? $reqData['docType'] : null,
          ])
      );
      $route = "/api/v1/hotrender/{$renderRequest['requestId']}";
      return $response->withStatus(201)
          ->withHeader('Location', $route)
          ->withJson([
              'requestId' => $renderRequest['requestId'],
              'createdAt' => $renderRequest['createdAt'],
              'location' => $route
          ]);
    } catch (InvalidHotRenderRequestException $e) {
      return $response->withStatus(400)->withJson([
          'status' => 400,
          'errors' => [$e->getMessage()],
          'date' => new \DateTime(),
      ]);
    } catch (CompilationException $e) {
      return $response->withStatus(409)->withJson([
          'status' => 400,
          'errors' => [$e->getMessage()],
          'data' => new \DateTime(),
          'docType' => $reqData['docType']
      ]);
    }
  }
}
