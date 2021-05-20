<?php declare(strict_types=1);


namespace HomeCEU\DTS\Api\Middleware;


use HomeCEU\DTS\Api\App;

class Loader {
  public static function load(App $app): void {
    $app->add(OptionsMiddleware::class)
        ->add(CORSMiddleware::class);
  }
}
