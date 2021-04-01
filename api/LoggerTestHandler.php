<?php declare(strict_types=1);


namespace HomeCEU\DTS\Api;


use Monolog\Handler\StreamHandler;

class LoggerTestHandler {
  public static function create(): StreamHandler {
    $logFile = Logger::logDir() . '/test.log';
    $h = new StreamHandler($logFile, \Monolog\Logger::ERROR);
    $h->setFormatter(Logger::monologFormatter());
    return $h;
  }
}
