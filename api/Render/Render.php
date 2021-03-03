<?php


namespace HomeCEU\DTS\Api\Render;

use HomeCEU\DTS\Api\DiContainer;
use HomeCEU\DTS\Persistence\CompiledTemplatePersistence;
use HomeCEU\DTS\Persistence\DocDataPersistence;
use HomeCEU\DTS\Persistence\TemplatePersistence;
use HomeCEU\DTS\Repository\DocDataRepository;
use HomeCEU\DTS\Repository\RecordNotFoundException;
use HomeCEU\DTS\Repository\TemplateRepository;
use HomeCEU\DTS\UseCase\Render\InvalidRenderRequestException;
use HomeCEU\DTS\UseCase\Render\Render as RenderUseCase;
use HomeCEU\DTS\UseCase\Render\RenderRequest;
use Negotiation\Accept;
use Negotiation\Negotiator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\NotFoundException;
use Slim\Http\Stream;

class Render {
  private DocDataRepository $dataRepo;
  private RenderUseCase $useCase;
  private TemplateRepository $templateRepo;

  public function __construct(DiContainer $di) {
    $db = $di->dbConnection;

    $this->templateRepo = new TemplateRepository(
        new TemplatePersistence($db),
        new CompiledTemplatePersistence($db)
    );
    $this->dataRepo = new DocDataRepository(new DocDataPersistence($db));
    $this->useCase = new RenderUseCase($this->templateRepo, $this->dataRepo);
  }

  public function __invoke(Request $request, Response $response, $args) {
    try {
      $renderRequest = $this->renderRequestFromArgs($request, $args);
      $renderResponse = $this->useCase->renderDoc($renderRequest);
      return $response
          ->withHeader('Content-Type', $renderResponse->contentType)
          ->withBody(new Stream(fopen($renderResponse->path, 'r')))
          ->withStatus(200);
    } catch (RecordNotFoundException $e) {
      throw new NotFoundException($request, $response);
    } catch (InvalidRenderRequestException $e) {
      return $response->withStatus(400, "Invalid Render Request, not enough information");
    }
  }

  protected function renderRequestFromArgs(Request $request, $args): RenderRequest {
    $params = array_merge($request->getQueryParams(), $args);
    if (empty($params['format'])) {
      $params['format'] = $this->determineFormat($request);
    }
    return RenderRequest::fromState($params);
  }


  protected function determineFormat(Request $request) {
    $negotiator = new Negotiator();

    $acceptHeader = $request->getHeader('Accept');
    $priorities   = array('text/html', 'application/pdf', 'text/plain;q=0.5');

    /** @var Accept $mediaType */
    $mediaType = $negotiator->getBest($acceptHeader[0], $priorities);
    switch ($mediaType->getValue()) {
      case "application/pdf": return "pdf";
    }
    return "html";
  }
}
