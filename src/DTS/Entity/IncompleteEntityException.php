<?php declare(strict_types=1);


namespace HomeCEU\DTS\Entity;


class IncompleteEntityException extends \Exception {
  public function __construct($message = "", array $keys = [], $code = 0) {
    parent::__construct($message, $code);

    if (!empty($keys)) {
      $keyStr = "Required Keys: " . implode(', ', $keys);
      $this->message = empty($message) ? $keyStr : "{$message} | {$keyStr}";
    }
  }
}
