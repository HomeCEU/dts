<?php declare(strict_types=1);


namespace HomeCEU\DTS\Render;


class CompilationException extends \Exception {
  const ERROR_MISSING_PARTIAL = 86;
  const ERROR_SYNTAX_ERROR = 75;
  const ERROR_INVALID_VARIABLE = 309;

  public array $errors;

  private function __construct($message, $code, array $errors = []) {
    parent::__construct($message, $code);
    $this->errors = $errors;
  }

  public static function create(string $message, int $code, array $errors = []): self {
    if (!empty($errors) && !is_array($errors[0])) {
      $errors = [$errors];
    }
    return new self($message, $code, $errors);
  }

  public static function fromException(\Exception $e, array $errors = []): self {
    return self::create(self::extractErrorMessage($e), self::getErrorCode($e), $errors);
  }

  private static function extractErrorMessage(\Exception $e): string {
    $message = $e->getMessage();
    if (strpos($message, 'Wrong variable naming') === 0) {
      return strtok($message, "\n");
    }
    if (strpos($message, 'Can not find partial for') === 0) {
      return 'One or more required partials cannot be found. Please check your source code or create the missing partials';
    }
    return $message;
  }

  private static function getErrorCode(\Exception $e): int {
    $message = $e->getMessage();
    if (strpos($message, 'Can not find partial for') === 0) {
      return self::ERROR_MISSING_PARTIAL;
    }
    if (strpos($message, 'Wrong variable naming') === 0) {
      return self::ERROR_INVALID_VARIABLE;
    }
    return self::ERROR_SYNTAX_ERROR;
  }
}
