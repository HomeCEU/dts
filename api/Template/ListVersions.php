<?php


namespace HomeCEU\DTS\Api\Template;


use Exception;
use HomeCEU\DTS\Entity\Template;
use HomeCEU\DTS\Persistence\CompiledTemplatePersistence;
use HomeCEU\DTS\Persistence\TemplatePersistence;
use HomeCEU\DTS\Repository\TemplateRepository;
use HomeCEU\DTS\UseCase\TemplateVersionList;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response;

class ListVersions {

  private $useCase;

  public function __construct(ContainerInterface $diContainer) {
    $db = $diContainer->dbConnection;
    $repo = new TemplateRepository(
        new TemplatePersistence($db),
        new CompiledTemplatePersistence($db)
    );
    $this->useCase = new TemplateVersionList($repo);
  }

  public function __invoke(Request $request, Response $response, $args) {
    try {
      $templates = $this->useCase->getVersions($args['docType'], $args['templateKey']);
      $responseData = [
          'total' => count($templates),
          'items' => array_map(function (Template $t) {
            return TemplateResponseHelper::templateDetailModel($t);
          },$templates)
      ];
      return $response
          ->withStatus(200)
          ->withJson($responseData);
    } catch (Exception $e) {
      return $response->withStatus(500, "failed to list templates");
    }
  }
}