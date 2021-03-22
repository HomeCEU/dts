<?php declare(strict_types=1);


namespace HomeCEU\DTS\Api;


use Monolog\Handler\StreamHandler;

class LoggerAppHandler {
  public static function create(): StreamHandler {
    $logFile = Logger::logDir() . '/dts.log';
    $h = new StreamHandler($logFile, \Monolog\Logger::NOTICE);
    $h->setFormatter(Logger::monologFormatter());
    return $h;
  }
}
