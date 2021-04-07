<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase;


use HomeCEU\DTS\Entity\Partial;
use HomeCEU\DTS\Entity\Template;
use HomeCEU\DTS\Render\Partial as RenderPartial;
use HomeCEU\DTS\Render\TemplateCompiler;
use HomeCEU\DTS\Repository\PartialRepository;
use HomeCEU\DTS\Repository\TemplateRepository;

class AddTemplate {
  private TemplateCompiler $compiler;
  private TemplateRepository $repository;
  private PartialRepository $partialRepository;

  public function __construct(TemplateRepository $repository, PartialRepository $partialRepository) {
    $this->compiler = TemplateCompiler::create();
    $this->repository = $repository;
    $this->partialRepository = $partialRepository;
  }

  public function addTemplate(AddTemplateRequest $request): Template {
    $template = $this->repository->createNewTemplate($request->docType, $request->templateKey, $request->author, $request->body);
    $this->repository->save($template);
    $this->addCompiled($template);

    return $template;
  }

  private function addCompiled(Template $template): void {
    $renderPartials = array_map(function (Partial $partial) {
      return new RenderPartial($partial->name, $partial->body);
    }, $this->partialRepository->findByDocType($template->docType));

    $this->compiler->setPartials($renderPartials);
    $this->repository->saveCompiled($template, $this->compiler->compile($template->body));
  }
}
