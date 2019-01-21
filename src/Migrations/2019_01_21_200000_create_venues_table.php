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
            $t->unsignedBigInteger('identifier');
            $t->string('name', 255);
            $t->mediumText('address');
            $t->string('city', 255);
            $t->string('postcode', 8);
            $t->string('telephone', 255);
            $t->mediumText('website');
            $t->unsignedSmallInteger('crowd_capacity');
            $t->mediumText('travel_details');

            $t->timestamps();
        });
    }

    public function down()
    {
        \Schema::drop('venues');
    }
}