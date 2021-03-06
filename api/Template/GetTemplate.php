<?php declare(strict_types=1);


namespace HomeCEU\DTS\Api\Template;


use HomeCEU\DTS\Persistence\CompiledTemplatePersistence;
use HomeCEU\DTS\Persistence\TemplatePersistence;
use HomeCEU\DTS\Repository\RecordNotFoundException;
use HomeCEU\DTS\Repository\TemplateRepository;
use HomeCEU\DTS\UseCase\GetTemplate as GetTemplateUseCase;
use HomeCEU\DTS\UseCase\GetTemplateRequest;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\NotFoundException;

class GetTemplate {
  private GetTemplateUseCase $useCase;

  public function __construct(ContainerInterface $container) {
    $conn = $container->get('dbConnection');

    $repo = new TemplateRepository(
        new TemplatePersistence($conn),
        new CompiledTemplatePersistence($conn)
    );
    $this->useCase = new GetTemplateUseCase($repo);
  }

  public function __invoke(Request $request, Response $response, $args): ResponseInterface {
    try {
      $template = $this->useCase->getTemplate(
          GetTemplateRequest::fromState([
              'id' => $args['templateId'] ?? '',
              'docType' => $args['docType'] ?? '',
              'key' => $args['templateKey'] ?? ''
          ])
      );
      $response->getBody()->write($template->body);
      return $response->withStatus(200);
    } catch (RecordNotFoundException $e) {
      throw new NotFoundException($request, $response);
    }
  }
}
