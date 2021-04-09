<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase;


use HomeCEU\DTS\Entity\PartialBuilder;
use HomeCEU\DTS\Render\PartialInterface;
use HomeCEU\DTS\Render\TemplateCompiler;
use HomeCEU\DTS\Repository\PartialRepository;
use HomeCEU\DTS\Repository\TemplateRepository;

class AddPartial {
  private TemplateCompiler $compiler;
  private PartialRepository $partialRepository;
  private TemplateRepository $templateRepository;

  public function __construct(PartialRepository $partialRepository, TemplateRepository $templateRepository) {
    $this->compiler = new TemplateCompiler();
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

  private function compileTemplatesForDocType(string $docType) {
    $this->compiler->setPartials($this->partialRepository->findByDocType($docType));

    foreach ($this->templateRepository->findByDocType($docType) as $template) {
      $ct = $this->compiler->compile($template->body);
      $this->templateRepository->saveCompiled($template, $ct);
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
