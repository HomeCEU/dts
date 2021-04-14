<?php


namespace HomeCEU\DTS\UseCase\Render;


use HomeCEU\DTS\AbstractEntity;
use HomeCEU\DTS\Repository\DocDataRepository;
use HomeCEU\DTS\Repository\TemplateRepository;
use HomeCEU\DTS\UseCase\Exception\InvalidRenderRequestException;

class Render {
  use RenderServiceTrait;
  private DocDataRepository $docDataRepo;
  private TemplateRepository $templateRepo;
  public AbstractEntity $originalRequest;
  public AbstractEntity $completeRequest;

  public function __construct(TemplateRepository $templateRepo, DocDataRepository $docDataRepo) {
    $this->docDataRepo = $docDataRepo;
    $this->templateRepo = $templateRepo;
  }

  public function renderDoc(RenderRequest $request): AbstractEntity {
    if (!$request->isValid()) {
      throw new InvalidRenderRequestException;
    }
    $this->originalRequest = $request;
    $this->completeRequest = $this->buildRequestOfIds($request);
    return $this->renderTemplate();
  }

  protected function buildRequestOfIds(RenderRequest $request): AbstractEntity {
    return RenderRequest::fromState([
        'id' => $this->getDataIdFromRequest($request),
        'templateId' => $this->getTemplateIdFromRequest($request),
        'format' => $request->get('format'),
    ]);
  }

  private function getDataIdFromRequest(RenderRequest $request): string {
    if (!empty($request->id)) {
      return $request->id;
    }
    return $this->docDataRepo->lookupId($request->docType, $request->key);
  }

  private function getTemplateIdFromRequest(RenderRequest $request): string {
    if (!empty($request->templateId)) {
      return $request->templateId;
    }
    return $this->templateRepo->lookupId($request->docType, $request->templateKey);
  }

  private function renderTemplate(): AbstractEntity {
    $service = $this->getRenderService($this->completeRequest->format);
    $template = $this->templateRepo->getCompiledTemplateById($this->completeRequest->templateId);
    $docData = $this->docDataRepo->getByDocDataId($this->completeRequest->id);

    return RenderResponse::fromState([
        'path' => $service->render($template->body, $docData->data),
        'contentType' => $service->getContentType()
    ]);
  }
}
