<?php


namespace HomeCEU\DTS\UseCase\Render;


use HomeCEU\DTS\Repository\DocDataRepository;
use HomeCEU\DTS\Repository\TemplateRepository;

class Render {
  use RenderServiceTrait;

  /** @var DocDataRepository */
  private $docDataRepo;

  /** @var TemplateRepository  */
  private $templateRepo;

  /** @var RenderRequest  */
  public $originalRequest;

  /** @var RenderRequest  */
  public $completeRequest;

  public function __construct(TemplateRepository $templateRepo, DocDataRepository $docDataRepo) {
    $this->docDataRepo = $docDataRepo;
    $this->templateRepo = $templateRepo;
  }

  public function renderDoc(RenderRequest $request): RenderResponse {
    if (!$request->isValid()) {
      throw new InvalidRenderRequestException;
    }
    $this->originalRequest = $request;
    $this->completeRequest = $this->buildRequestOfIds($request);
    return $this->renderTemplate();
  }

  protected function buildRequestOfIds(RenderRequest $request): RenderRequest {
    return RenderRequest::fromState([
        'dataId' => $this->getDataIdFromRequest($request),
        'templateId' => $this->getTemplateIdFromRequest($request),
        'format' => $request->format,
    ]);
  }

  private function getDataIdFromRequest(RenderRequest $request) {
    if (!empty($request->dataId)) {
      return $request->dataId;
    }
    return $this->docDataRepo->lookupId($request->docType, $request->dataKey);
  }

  private function getTemplateIdFromRequest(RenderRequest $request) {
    if (!empty($request->templateId)) {
      return $request->templateId;
    }
    return $this->templateRepo->lookupId($request->docType, $request->templateKey);
  }

  private function renderTemplate(): RenderResponse {
    $service = $this->getRenderService($this->completeRequest->format);
    $template = $this->templateRepo->getCompiledTemplateById($this->completeRequest->templateId);
    $docData = $this->docDataRepo->getByDocDataId($this->completeRequest->dataId);

    return RenderResponse::fromState([
        'path' => $service->render($template->body, $docData->data),
        'contentType' => $service->getContentType()
    ]);
  }
}
