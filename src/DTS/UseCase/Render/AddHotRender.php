<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase\Render;


use HomeCEU\DTS\Render\TemplateCompiler;
use HomeCEU\DTS\Render\TemplateHelpers;
use HomeCEU\DTS\Repository\HotRenderRepository;
use HomeCEU\DTS\Repository\PartialRepository;
use HomeCEU\DTS\Repository\TemplateRepository;

class AddHotRender {
  private TemplateCompiler $compiler;
  private HotRenderRepository $repository;
  private TemplateRepository $templateRepository;
  private PartialRepository $partialRepository;

  public function __construct(
      HotRenderRepository $repository,
      TemplateRepository $templateRepository,
      PartialRepository $partialRepository
  ) {
    $this->compiler = TemplateCompiler::create();
    $this->repository = $repository;
    $this->templateRepository = $templateRepository;
    $this->partialRepository = $partialRepository;
  }

  public function add(AddHotRenderRequest $request): array {
    $this->loadPartials($request);

    $compiled = $this->compiler->compile($request->template);
    $request = $this->repository->newHotRenderRequest($compiled, $request->data);
    $this->repository->save($request);

    return $request->toArray();
  }

  private function loadPartials(AddHotRenderRequest $request): void {
    if (empty($request->docType)) {
      $this->compiler->ignoreMissingPartials();
    }
    $this->compiler->setPartials($this->partialRepository->findByDocType($request->docType));
  }
}
