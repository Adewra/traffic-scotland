<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVenuesTable extends Migration
{
    public function up()
    {
        \Schema::create('venues', function(Blueprint $t)
        {
            $t->bigIncrements('id');
            $t->unsignedBigInteger('identifier')->unique();
            $t->string('venueName', 255)->nullable();
            $t->string('address1', 255)->nullable();
            $t->string('address2', 255)->nullable();
            $t->string('address3', 255)->nullable();
            $t->string('city', 255);
            $t->string('postCode', 8);
            $t->string('telephone', 255)->nullable();
            $t->string('webAddress', 1024)->nullable();
            $t->string('emailAddress', 1024)->nullable();
            $t->unsignedSmallInteger('venueCapacity')->nullable();
            $t->unsignedInteger('locationX')->nullable();
            $t->unsignedInteger('locationY')->nullable();
            $t->decimal('latitude')->nullable();
            $t->decimal('longitude')->nullable();

            $t->timestamps();
        });
    }

    public function down()
    {
        \Schema::drop('venues');
    }
}