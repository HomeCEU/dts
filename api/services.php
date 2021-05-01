<?php

namespace HomeCEU\DTS\Api;

use HomeCEU\DTS\Db;
use HomeCEU\DTS\Db\Config;

// this array gets passed into Slim\Container
return [
    'settings' => [
        'addContentLengthHeader' => false,
    ],
    'dbConfig' => function ($container) {
      return Config::fromEnv();
    },
    'dbConnection' => function($container) {
      return Db::connection();
    },
    'logger' => function($container) {
      return Logger::instance();
    },
    'errorHandler' => function ($c) {
      return new ErrorHandler($c);
    },
    'phpErrorHandler' => function ($c) {
      return new ErrorHandler($c);
    }
];