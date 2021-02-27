<?php


namespace HomeCEU\DTS\Api;


use Psr\Container\ContainerInterface;

class App extends \Slim\App {
  private const ROUTES_V1 = APP_ROOT . '/api/routes_v1.php';
  private const SERVICES_FILE = APP_ROOT . '/api/services.php';

  /** @var DiContainer */
  private $_di;

  public function __construct(ContainerInterface $diContainer = null) {
    $this->_di = $diContainer ?: $this->diContainer();
    if (getenv('APP_ENV') == 'dev') {
      $this->devMode();
    }
    parent::__construct($this->_di);
    $this->loadRoutes('v1', self::ROUTES_V1);
  }

  private function devMode() {
    $logFile = Logger::logDir() . "/php-error.log";
    error_reporting(E_ALL);
    ini_set("log_errors", 1);
    ini_set("error_log", $logFile);
  }

  private function diContainer() {
    return new DiContainer();
  }

  private function loadRoutes(string $version, string $routesPath) {
    $routes = include $routesPath;
    foreach ($routes as $route) {
      $this->mapRoute($route, $version);
    }
  }

  private function mapRoute(Route $route, string $version): void {
    $this->group('/api', function () use ($route, $version) {
      $this->group('/' . $version, function () use ($route) {
        $this->map($route->methods, $route->uri, $route->function);
      });
    });
    $this->mapDeprecatedRoute($route);
  }

  /**
   * @param Route $route
   * @deprecated these routes do not begin with /api/v*, and will be deprecated before the next major release
   */
  private function mapDeprecatedRoute(Route $route): void {
    $this->map($route->methods, $route->uri, $route->function);
  }
}
