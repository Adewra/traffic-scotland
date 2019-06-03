<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    public function up()
    {
        \Schema::create('events', function(Blueprint $t)
        {
            $t->bigIncrements('id');
            $t->unsignedBigInteger('identifier')->unique();
            $t->string('name', 255);
            $t->dateTime('date');
            $t->dateTime('start_time');
            $t->dateTime('end_time');
            $t->mediumText('link');
            $t->string('icon', 255);
            $t->mediumText('description');
            $t->smallInteger('historic_attendance')->nullable();
            $t->dateTime('last_updated_by_provider');
            //$t->unsignedBigInteger('venue_id')->nullable();

            //$t->foreign('venue_id')->references('id')->on('venues');
            $t->timestamps();
        });
    }

    public function down()
    {
        \Schema::drop('events');
    }
}