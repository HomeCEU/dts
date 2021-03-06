<?php


namespace HomeCEU\DTS\Api\Template;


use HomeCEU\DTS\Api\DiContainer;
use HomeCEU\DTS\Persistence\CompiledTemplatePersistence;
use HomeCEU\DTS\Persistence\TemplatePersistence;
use HomeCEU\DTS\Repository\TemplateRepository;
use HomeCEU\DTS\UseCase\ListTemplates as ListTemplatesUseCase;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response;

class ListDocTypes {
  private ListTemplatesUseCase $useCase;

  public function __construct(DiContainer $di) {
    $db = $di->dbConnection;
    $repo = new TemplateRepository(
        new TemplatePersistence($db),
        new CompiledTemplatePersistence($db)
    );
    $this->useCase = new ListTemplatesUseCase($repo);
  }

  public function __invoke(Request $request, Response $response, $args) {
    $docTypes = $this->useCase->getDocTypes();
    $responseData = [
        'total' => count($docTypes),
        'items' => array_map(function ($row) {
          return [
              'docType' => $row['docType'],
              'templateCount' => $row['templateCount'],
              'links' => [
                  'templates' => "/api/v1/template?filter[type]={$row['docType']}"
              ]
          ];
        }, $docTypes)
    ];
    return $response
        ->withStatus(200)
        ->withJson($responseData);
  }
}
