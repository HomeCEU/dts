<?php


namespace HomeCEU\DTS\Api\Template;

use HomeCEU\DTS\Api\ResponseHelper;
use HomeCEU\DTS\Entity\Template;
use HomeCEU\DTS\Persistence\CompiledTemplatePersistence;
use HomeCEU\DTS\Persistence\TemplatePersistence;
use HomeCEU\DTS\Repository\TemplateRepository;
use HomeCEU\DTS\UseCase\ListTemplates as ListTemplatesUseCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response;

class ListTemplates {
  private ListTemplatesUseCase $useCase;

  public function __construct(ContainerInterface $diContainer) {
    $db = $diContainer->dbConnection;
    $repo = new TemplateRepository(
        new TemplatePersistence($db),
        new CompiledTemplatePersistence($db)
    );
    $this->useCase = new ListTemplatesUseCase($repo);
  }

  public function __invoke(Request $request, Response $response, $args) {
    $queryParams = $request->getQueryParams();
    $filter = empty($queryParams['filter']) ? [] : $queryParams['filter'];
    $templates = $this->getTemplates($filter);

    $responseData = [
        'total' => count($templates),
        'items' => array_map(function (Template $t) {
          return ResponseHelper::templateDetailModel($t);
        },$templates)
    ];
    return $response
        ->withStatus(200)
        ->withJson($responseData);
  }

  protected function getTemplates($filter=[]) {
    if (!empty($filter['search'])) {
      return $this->useCase->search($filter['search']);
    }
    if (!empty($filter['type'])) {
      return $this->useCase->filterByType($filter['type']);
    }
    return $this->useCase->all();
  }
}
