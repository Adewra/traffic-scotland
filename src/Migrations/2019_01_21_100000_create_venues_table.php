<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVenuesTable extends Migration
{
    public function up()
    {
        \Schema::create('venues', function(Blueprint $t)
        {
            $t->unsignedBigInteger('id')->autoIncrement();
            $t->unsignedBigInteger('identifier')->unique();
            $t->string('name', 255);
            $t->mediumText('address')->nullable();
            $t->string('city', 255);
            $t->string('postcode', 8);
            $t->string('telephone', 255)->nullable();
            $t->mediumText('website')->nullable();
            $t->string('email', 1024)->nullable();
            $t->unsignedSmallInteger('crowd_capacity')->nullable();
            $t->mediumText('link');

            $t->timestamps();
        });
    }

    public function down()
    {
        \Schema::drop('venues');
    }
}