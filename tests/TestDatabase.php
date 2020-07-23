<?php

namespace PhpSquad\NavDirectory\Tests;

use Illuminate\Database\Capsule\Manager;

class TestDatabase
{

    public static function connect(): Manager
    {
        $capsule = new Manager;

        $capsule->addConnection([
            'driver' => 'sqlite',
            'database' => __DIR__.'/../database.sqlite',
            'prefix' => ''
        ]);

        $capsule->bootEloquent();
        $capsule->setAsGlobal();

        return $capsule;
    }

}