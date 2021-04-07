<?php declare(strict_types=1);


namespace HomeCEU\DTS;


use HomeCEU\DTS\Entity\GetPropertyTrait;

abstract class AbstractEntity implements Entity {
  use GetPropertyTrait;

  public function toArray(): array {
    $result = [];
    foreach ($this->keys() as $k) {
      $result[$k] = $this->{$k};
    }
    return $result;
  }

  /**
   * @param array $state
   * @return static (because 'static' isn't a valid return type in PHP7.4, we need this docblock for the IDE)
   */
  public static function fromState(array $state): self {
    $entity = new static();
    foreach ($entity->keys() as $k) {
      if (array_key_exists($k, $state)) {
        $entity->{$k} = static::valueFromState($state, $k);
      }
    }
    return $entity;
  }

  protected function keys(): array {
    return array_keys(get_class_vars(static::class));
  }

  protected static function valueFromState(array $state, string $key) {
    if ($key == 'createdAt' && is_string($state[$key]))
      return new \DateTime($state[$key]);
    return $state[$key];
  }
}
