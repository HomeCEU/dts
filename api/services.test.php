<?php

namespace HomeCEU\DTS\Api;

use HomeCEU\DTS\Db;
use HomeCEU\DTS\Db\Config;
use Nette\Caching\Cache;
use Nette\Caching\Storages\DevNullStorage;

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
      return Logger::testInstance();
    },
    'errorHandler' => function ($c) {
      return new ErrorHandler($c);
    },
    'phpErrorHandler' => function ($c) {
      return new ErrorHandler($c);
    },
    'cache' => function () {
      return new Cache(new DevNullStorage(), 'DTS_Tests');
    },
];
