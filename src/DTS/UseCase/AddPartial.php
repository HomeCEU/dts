<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase;


use HomeCEU\DTS\Repository\PartialRepository;

class AddPartial {
  private PartialRepository $repository;

  public function __construct(PartialRepository $repository) {
    $this->repository = $repository;
  }

  /**
   * Add a new partial to the database
   *
   * @param AddPartialRequest $request
   * @return string New Partial ID
   */
  public function add(AddPartialRequest $request): string {
    $partial = $this->repository->create(
        $request->docType,
        $request->name,
        $request->author,
        $request->body
    );
    return $this->repository->save($partial);
  }
}
