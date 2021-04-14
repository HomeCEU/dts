<?php declare(strict_types=1);


namespace HomeCEU\DTS\Api\Template;


use HomeCEU\DTS\Persistence\CompiledTemplatePersistence;
use HomeCEU\DTS\Persistence\PartialPersistence;
use HomeCEU\DTS\Persistence\TemplatePersistence;
use HomeCEU\DTS\Render\CompilationException;
use HomeCEU\DTS\Render\RenderHelper;
use HomeCEU\DTS\Repository\PartialRepository;
use HomeCEU\DTS\Repository\TemplateRepository;
use HomeCEU\DTS\UseCase\AddTemplate as AddTemplateUseCase;
use HomeCEU\DTS\UseCase\AddTemplateRequest;
use HomeCEU\DTS\UseCase\Exception\InvalidAddTemplateRequestException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\Request;

class AddTemplate {
  private AddTemplateUseCase $useCase;

  public function __construct(ContainerInterface $container) {
    $conn = $container->get('dbConnection');

    $templateRepository = new TemplateRepository(
        new TemplatePersistence($conn),
        new CompiledTemplatePersistence($conn)
    );
    $partialRepository = new PartialRepository(new PartialPersistence($conn));
    $this->useCase = new AddTemplateUseCase($templateRepository, $partialRepository);
  }

  public function __invoke(Request $request, Response $response): ResponseInterface {
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
    } catch (CompilationException $e) {
      $errors = ['message' => $e->getMessage()];
      if ($e->getCode() === $e::ERROR_MISSING_PARTIAL) {
        $errors['required_partials'] = RenderHelper::extractPartials($addRequest->body);
      }
      return $response->withStatus(409)->withJson([
          'message' => 'Cannot compile template',
          'errors' => [$errors]
      ]);
    }
  }
}
