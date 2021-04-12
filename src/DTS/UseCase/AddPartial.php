<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase;


use HomeCEU\DTS\Entity\PartialBuilder;
use HomeCEU\DTS\Render\CompilationException;
use HomeCEU\DTS\Render\PartialInterface;
use HomeCEU\DTS\Render\TemplateCompiler;
use HomeCEU\DTS\Repository\PartialRepository;
use HomeCEU\DTS\Repository\TemplateRepository;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class AddPartial {
  private TemplateCompiler $compiler;
  private PartialRepository $partialRepository;
  private TemplateRepository $templateRepository;

  public function __construct(PartialRepository $partialRepository, TemplateRepository $templateRepository) {
    $this->compiler = TemplateCompiler::create();
    $this->partialRepository = $partialRepository;
    $this->templateRepository = $templateRepository;
  }

  public function add(AddPartialRequest $request): PartialInterface {
    $partial = $this->createPartialFromRequest($request);
    $this->savePartial($partial);
    $this->compileTemplatesForDocType($request->docType);

    return $partial;
  }

  private function savePartial(PartialInterface $partial): void {
    $this->partialRepository->save($partial);
  }

  private function compileTemplatesForDocType(string $docType): void {
    $partials = $this->partialRepository->findByDocType($docType);
    $this->compiler->setPartials($partials);

    $partials = array_map(function (PartialInterface $partial) {
      return ['id' => $partial->get('id'), 'key' => $partial->get('name')];
    }, $partials);

    $errors = [];
    foreach ($this->templateRepository->findByDocType($docType) as $template) {
      try {
        $ct = $this->compiler->compile($template->body);
        $this->templateRepository->saveCompiled($template, $ct);
      } catch (CompilationException $e) {
        $errors[] = [
            'template' => [
                'id' => $template->templateId,
                'key' => $template->templateKey,
            ],
            'partials' => $partials
        ];
      }
    }
    if (!empty($errors)) {
      throw new CompilationException($e->getMessage(), $errors);
    }
  }

  private function createPartialFromRequest(AddPartialRequest $request): PartialInterface {
    return PartialBuilder::create()
        ->withName($request->name)
        ->withBody($request->body)
        ->withDocType($request->docType)
        ->withAuthor($request->author)
        ->withMetadata($request->metadata)
        ->build();
  }
}
