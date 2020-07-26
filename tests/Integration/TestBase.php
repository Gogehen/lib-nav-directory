<?php

namespace PhpSquad\NavDirectory\Tests\Integration;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;

Trait TestBase
{
    public function createTables()
    {
        Manager::schema()->dropAllTables();


        Manager::schema()->create(self::NAV_DIRECTORY_TABLE, function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->uuid('account_id');
            $table->uuid('user_id');
            $table->string('type');
            $table->string('name');
            $table->uuid('parent_id');
            $table->string('icon')->default('none');
            $table->timestamps();
        });
    }

    public static function insertTestData()
    {
    }
}