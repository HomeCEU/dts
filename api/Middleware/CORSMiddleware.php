<?php declare(strict_types=1);


namespace HomeCEU\DTS\Api\Middleware;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class CORSMiddleware
 *
 * Enable CORS from any domain
 * This API is used one of two ways, a client in a codebase, or from a web based client
 * hosted somewhere other than on the server this is hosted on. CORS is a reality for this,
 * we'll protect it with tokens.
 *
 * @author Dan McAdams
 * @package HomeCEU\DTS\Api\Middleware
 */
class CORSMiddleware {
  public function __invoke(Request $request, Response $response, callable $next): Response {
    $headers = $request->getHeaders();
    $serverParams = $request->getServerParams();
    $origin = $headers['HTTP_ORIGIN'] ?? $serverParams['HTTP_HOST'];
    $response = $response->withHeader('Access-Control-Allow-Origin', $origin)
        ->withHeader('Access-Control-Allow-Credentials', 'true')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept');
    return $next($request, $response);
  }
}
