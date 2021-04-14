<?php declare(strict_types=1);


namespace HomeCEU\DTS\UseCase\Render;

use HomeCEU\DTS\Render\CompilationException;

/**
 * Class CompilationErrorHandler
 *
 * Converts a standard exception (\Exception) into a
 * CompilationException with a proper code, and message
 *
 * @author Dan McAdams
 * @package HomeCEU\DTS\UseCase\Render
 */
class CompilationExceptionBuilder {
  public static function exception(\Exception $e): CompilationException {
    return new CompilationException($e->getMessage(), 0);
  }
}
