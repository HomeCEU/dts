<?php declare(strict_types=1);


namespace HomeCEU\DTS\Api\Render;


use HomeCEU\DTS\Persistence\HotRenderPersistence;
use HomeCEU\DTS\Repository\HotRenderRepository;
use HomeCEU\DTS\Repository\RecordNotFoundException;
use HomeCEU\DTS\UseCase\Render\HotRender as HotRenderUseCase;
use HomeCEU\DTS\UseCase\Render\HotRenderRequest;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\NotFoundException;
use Slim\Http\Stream;

class HotRender {
  private HotRenderUseCase $useCase;

  public function __construct(ContainerInterface $container) {
    $conn = $container->get('dbConnection');
    $repo = new HotRenderRepository(new HotRenderPersistence($conn));
    $this->useCase = new HotRenderUseCase($repo);
  }

  public function __invoke(Request $request, Response $response, $args) {
    try {
      $qp = $request->getQueryParams();
      $r = HotRenderRequest::fromState([
          'requestId' => $args['requestId'],
          'format' => empty($qp['format']) ? 'html' : $qp['format']
      ]);
      $renderResponse = $this->useCase->render($r);
      return $response
          ->withHeader('Content-Type', $renderResponse->contentType)
          ->withBody(new Stream(fopen($renderResponse->path, 'r')))
          ->withStatus(200);
    } catch (RecordNotFoundException $e) {
      throw new NotFoundException($request, $response);
    }
  }
}
