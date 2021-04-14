<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase\Render;


use HomeCEU\DTS\Repository\HotRenderRepository;

class HotRender {
  use RenderServiceTrait;

  private $repository;

  public function __construct(HotRenderRepository $repository) {
    $this->repository = $repository;
  }

  public function render(HotRenderRequest $request): RenderResponse {
    $renderer = $this->getRenderService($request->format);
    $request = $this->repository->getById($request->id);

    return RenderResponse::fromState([
        'path' => $renderer->render($request->template, $request->data),
        'contentType' => $renderer->getContentType()
    ]);
  }
}
