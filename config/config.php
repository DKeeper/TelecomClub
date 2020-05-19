<?php

return [
    'language' => 'en',
    'baseUrl' => '/',
    'appUrl' => '/',
    'basePath' => dirname(__DIR__),
    'db' => [
        'dsn' => 'mysql:host=localhost;dbname=telecom_club',
        'user' => 'telecom_club',
        'password' => '',
    ],
    'cache' => [
        'class' => \components\Cache::class,
        'host' => 'localhost',
        'port' => 11211,
    ],
];
