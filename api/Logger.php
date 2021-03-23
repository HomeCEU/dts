<?php


namespace HomeCEU\DTS\Api;


use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Psr\Log\LoggerInterface;

class Logger {
  private static \Monolog\Logger $instance;

  public static function instance(): LoggerInterface {
    if (empty(self::$instance)) {
      $logger = new \Monolog\Logger('applog');
      $logger->pushHandler(LoggerAppHandler::create());
      self::$instance = $logger;
    }
    return self::$instance;
  }

  public static function testInstance(): LoggerInterface {
    self::initialize();
    return self::$instance->pushHandler(LoggerTestHandler::create());
  }

  public static function logDir(): string {
    $appLogDir = getenv('APP_LOG_DIR') ?: APP_ROOT.'/log';
    return substr($appLogDir, 0, 1) == '/'
        ? $appLogDir
        : APP_ROOT.'/'.$appLogDir;
  }

  protected static function initialize(): void {
    self::instance();
  }

  public static function monologFormatter(): FormatterInterface {
    $formatter = new LineFormatter(
        LineFormatter::SIMPLE_FORMAT,
        LineFormatter::SIMPLE_DATE
    );
    $formatter->includeStacktraces(true);
    return $formatter;
  }
}
