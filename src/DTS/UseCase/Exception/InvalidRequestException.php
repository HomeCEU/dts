<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase\Exception;


class InvalidRequestException extends \Exception {
  public function __construct($message = "", array $keys = [], $code = 0) {
    parent::__construct($message, $code);
    $this->message = empty($keys)
        ? $message
        : $message . "Required Keys: " . implode(', ', $keys);
  }
}
