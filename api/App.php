<?php


namespace HomeCEU\DTS\Api;


use Psr\Container\ContainerInterface;

class App extends \Slim\App {
  private const ROUTES_V1 = APP_ROOT . '/api/routes_v1.php';
  private DiContainer $_di;

  public function __construct(ContainerInterface $diContainer = null) {
    $this->_di = $diContainer ?: $this->diContainer();
    if (getenv('APP_ENV') == 'dev') {
      $this->devMode();
    }
    parent::__construct($this->_di);
    $this->loadRoutesForVersion('v1', self::ROUTES_V1);
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

  private function loadRoutesForVersion(string $version, string $routesPath) {
    $routes = include $routesPath;
    foreach ($routes as $route) {
      $this->mapRouteForVersion($route, $version);
    }
  }

  private function mapRouteForVersion(Route $route, string $version): void {
    $this->group('/api', function () use ($route, $version) {
      $this->group('/' . $version, function () use ($route) {
        $this->map($route->methods, $route->uri, $route->function);
      });
    });
    $this->mapLegacyRoute($route);
  }

  /**
   * Map routes without the /api/v* prefix
   *
   * @param Route $route
   * @deprecated to be removed in v1.2.0
   */
  private function mapLegacyRoute(Route $route): void {
    $this->map($route->methods, $route->uri, $route->function);
  }
}
