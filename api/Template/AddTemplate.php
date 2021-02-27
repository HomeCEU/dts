<?php declare(strict_types=1);


namespace HomeCEU\DTS\Api\Template;


use HomeCEU\DTS\Persistence\CompiledTemplatePersistence;
use HomeCEU\DTS\Persistence\TemplatePersistence;
use HomeCEU\DTS\Repository\TemplateRepository;
use HomeCEU\DTS\UseCase\AddTemplateRequest;
use HomeCEU\DTS\UseCase\InvalidAddTemplateRequestException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AddTemplate {
  private $useCase;

  public function __construct(ContainerInterface $container) {
    $conn = $container->dbConnection;

    $repo = new TemplateRepository(
        new TemplatePersistence($conn),
        new CompiledTemplatePersistence($conn)
    );
    $this->useCase = new \HomeCEU\DTS\UseCase\AddTemplate($repo);
  }

  public function __invoke(Request $request, Response $response) {
    try {
      /** @var AddTemplateRequest $addRequest */
      $addRequest = AddTemplateRequest::fromState($request->getParsedBody());
      $template = $this->useCase->addTemplate($addRequest);
      $route = "/api/v1/template/{$template->templateId}";

      return $response->withStatus(201)
          ->withHeader('Location', $route)
          ->withJson([
              'templateId' => $template->templateId,
              'templateKey' => $template->templateKey,
              'docType' => $template->docType,
              'author' => $template->author,
              'createdAt' => $template->createdAt,
              'bodyUri' => $route,
          ]);
    } catch (InvalidAddTemplateRequestException $e) {
      return $response->withStatus(400)->withJson(['errors' => "Invalid Request | {$e->getMessage()}"]);
    }
  }
}
