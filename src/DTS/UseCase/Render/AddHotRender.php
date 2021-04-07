<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase\Render;


use HomeCEU\DTS\Render\TemplateCompiler;
use HomeCEU\DTS\Render\TemplateHelpers;
use HomeCEU\DTS\Repository\HotRenderRepository;
use HomeCEU\DTS\Repository\TemplateRepository;
use HomeCEU\DTS\UseCase\Exception\InvalidHotRenderRequestException;

class AddHotRender {
  private TemplateCompiler $compiler;
  private HotRenderRepository $repository;
  private TemplateRepository $templateRepository;

  public function __construct(HotRenderRepository $repository, TemplateRepository $templateRepository) {
    $this->compiler = TemplateCompiler::create();
    $this->repository = $repository;
    $this->templateRepository = $templateRepository;
  }

  public function add(AddHotRenderRequest $request): array {
    $this->configureCompiler($request);

    $compiled = $this->compiler->compile($request->template);
    $request = $this->repository->newHotRenderRequest($compiled, $request->data);
    $this->repository->save($request);

    return $request->toArray();
  }

  private function configureCompiler(AddHotRenderRequest $request) {
    $this->compiler->addHelper(TemplateHelpers::equal());
    if (empty($request->docType)) {
      $this->compiler->ignoreMissingPartials();
      return;
    }
    $partials = array_merge(
        $this->templateRepository->findPartialsByDocType($request->docType),
        $this->templateRepository->findImagesByDocType($request->docType)
    );
    $this->compiler->setPartials($partials);
  }
}
