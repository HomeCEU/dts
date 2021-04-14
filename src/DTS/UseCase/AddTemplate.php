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
    $template = $this->repository->createNewTemplate(
        $request->docType,
        $request->templateKey,
        $request->author,
        $request->body
    );
    $compiled = $this->compileTemplate($template);
    $this->save($template, $compiled);
    return $template;
  }

  private function compileTemplate(Template $template): string {
    $renderPartials = array_map(function (Partial $partial) {
      return new RenderPartial($partial->key, $partial->body);
    }, $this->partialRepository->findByDocType($template->docType));

    $this->compiler->setPartials($renderPartials);
    return $this->compiler->compile($template->body);
  }

  private function save(Template $template, string $compiled) {
    $this->repository->save($template);
    $this->repository->saveCompiled($template, $compiled);
  }
}
