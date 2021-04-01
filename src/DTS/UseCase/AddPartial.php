<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase;


use HomeCEU\DTS\Entity\Partial;
use HomeCEU\DTS\Entity\PartialBuilder;
use HomeCEU\DTS\Repository\PartialRepository;

class AddPartial {
  private PartialRepository $repository;

  public function __construct(PartialRepository $repository) {
    $this->repository = $repository;
  }

  public function add(AddPartialRequest $request): Partial {
    $partial = PartialBuilder::create()
        ->withName($request->name)
        ->withBody($request->body)
        ->withDocType($request->docType)
        ->withAuthor($request->author)
        ->withMetadata($request->metadata)
        ->build();
    $this->repository->save($partial);
    return $partial;
  }
}
