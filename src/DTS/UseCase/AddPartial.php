<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase;


use HomeCEU\DTS\Entity\Partial;
use HomeCEU\DTS\Repository\PartialRepository;

class AddPartial {
  private PartialRepository $repository;

  public function __construct(PartialRepository $repository) {
    $this->repository = $repository;
  }

  public function add(AddPartialRequest $request): Partial {
    $partial = $this->repository->create(
        $request->docType,
        $request->name,
        $request->author,
        $request->body
    );
    $this->repository->save($partial);
    return $partial;
  }
}
