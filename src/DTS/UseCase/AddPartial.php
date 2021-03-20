<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase;


use HomeCEU\DTS\Entity\Partial;

class AddPartial {
  public function newPartial(AddPartialRequest $request): Partial {
    return new Partial();
  }
}
