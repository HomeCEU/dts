<?php

namespace HomeCEU\DTS\Api;

use HomeCEU\DTS\Db;
use HomeCEU\DTS\Db\Config;
use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;

// this array gets passed into Slim\Container
return [
    'settings' => [
        'addContentLengthHeader' => false,
    ],
    'dbConfig' => function ($container) {
      return Config::fromEnv();
    },
    'dbConnection' => function ($container) {
      return Db::connection();
    },
    'logger' => function ($container) {
      return Logger::instance();
    },
    'errorHandler' => function ($c) {
      return new ErrorHandler($c);
    },
    'phpErrorHandler' => function ($c) {
      return new ErrorHandler($c);
    },
    'cache' => function () {
      if (!is_dir(APP_ROOT . '/cache')) {
        mkdir(APP_ROOT . '/cache');
      }
      return new Cache(new FileStorage(APP_ROOT . '/cache'), 'app');
    },
];
