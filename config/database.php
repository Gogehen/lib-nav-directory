<?php

use Illuminate\Database\Capsule\Manager as capsule;

$capsule = new capsule;

$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => __DIR__.'/../database.sqlite',
    'prefix' => ''
]);

$capsule->bootEloquent();
$capsule->setAsGlobal();

return [
    'connections' => [
        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => __DIR__.'/../database.sqlite',
            'prefix'   => '',
        ]
    ],
];

