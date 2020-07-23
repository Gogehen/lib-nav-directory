<?php


namespace PhpSquad\NavDirectory\database;
use Illuminate\Database\Capsule\Manager as Capsule;

class Stutter
{
    public static function connect()
    {
        $capsule = new Capsule;

        $capsule->addConnection([

            "driver" => "sqlite",

            "host" =>":memory:",

            "database" => "",

            "username" => "",

            "password" => ""

        ]);

//Make this Capsule instance available globally.
        $capsule->setAsGlobal();

// Setup the Eloquent ORM.
        $capsule->bootEloquent();$capsule->bootEloquent();
    }

}