<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase;


use HomeCEU\DTS\Entity\PartialBuilder;
use HomeCEU\DTS\Render\CompilationException;
use HomeCEU\DTS\Render\PartialInterface;
use HomeCEU\DTS\Render\TemplateCompiler;
use HomeCEU\DTS\Repository\PartialRepository;
use HomeCEU\DTS\Repository\TemplateRepository;

class AddPartial {
  private TransactionalCompiler $compiler;
  private PartialRepository $partialRepository;

  public function __construct(PartialRepository $partialRepository, TemplateRepository $templateRepository) {
    $this->compiler = new TransactionalCompiler($templateRepository, $partialRepository);
    $this->partialRepository = $partialRepository;
  }

  public function add(AddPartialRequest $request): PartialInterface {
    $partial = $this->createPartialFromRequest($request);
    $this->assertCanCompile($partial);
    $this->savePartial($partial);
    $this->compiler->compileAllTemplatesForDocType($request->docType);

    return $partial;
  }

  private function savePartial(PartialInterface $partial): void {
    $this->partialRepository->save($partial);
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

  /**
   * Creates a template that requires the partial,
   * if it fails to compile, don't save it.
   *
   * @param PartialInterface $partial
   * @throws CompilationException
   */
  private function assertCanCompile(PartialInterface $partial) {
    try {
      TemplateCompiler::create()
          ->ignoreMissingPartials()
          ->addPartial($partial)
          ->compile("{{> {$partial->getName()} }}");
    } catch (CompilationException $e) {
      throw new CompilationException('Cannot compile partial', [$e->getMessage()]);
    }
  }
}
