<?php

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'production' => [
            'adapter' => getenv('DB_DRIVER')?:'mysql',
            'host'    => getenv('DB_HOST'),
            'name'    => getenv('DB_NAME'),
            'user'    => getenv('DB_USER'),
            'pass'    => getenv('DB_PASS'),
            'port'    => getenv('DB_PORT')?:3306,
            'charset' => getenv('DB_CHARSET')?:'utf8',
        ],
        'development' => [
            'adapter' => getenv('DB_DRIVER')?:'mysql',
            'host'    => getenv('DB_HOST'),
            'name'    => getenv('DB_NAME'),
            'user'    => getenv('DB_USER'),
            'pass'    => getenv('DB_PASS'),
            'port'    => getenv('DB_PORT')?:3306,
            'charset' => getenv('DB_CHARSET')?:'utf8',
        ],
        'testing' => [
            'adapter' => getenv('DB_DRIVER')?:'mysql',
            'host'    => getenv('DB_HOST'),
            'name'    => getenv('DB_NAME'),
            'user'    => getenv('DB_USER'),
            'pass'    => getenv('DB_PASS'),
            'port'    => getenv('DB_PORT')?:3306,
            'charset' => getenv('DB_CHARSET')?:'utf8',
        ]
    ],
    'version_order' => 'creation'
];
