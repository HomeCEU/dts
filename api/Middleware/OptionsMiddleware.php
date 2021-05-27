<?php declare(strict_types=1);


namespace HomeCEU\DTS\Api\Middleware;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class OptionsMiddleware
 *
 * Detects an OPTIONS request and responds with a 204.
 * This should be at the top of the Middleware stack, just under any CORSMiddleware
 *
 * @author Dan McAdams
 * @package HomeCEU\DTS\Api\Middleware
 */
class OptionsMiddleware {
  public function __invoke(Request $request, Response $response, callable $next): Response {
    if ($request->getMethod() === 'OPTIONS') {
      return $response->withStatus(204);
    }
    return $next($request, $response);
  }
}
