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
            $t->unsignedBigInteger('identifier');
            $t->string('name', 255);
            $t->dateTime('start_date');
            $t->dateTime('end_date');
            $t->mediumText('link');
            $t->dateTime('date');
            $t->string('icon', 255);
            $t->mediumText('description');
            $t->smallInteger('historic_attendance');
            $t->dateTime('last_updated_by_provider');
            $t->unsignedBigInteger('venue_id');

            $t->foreign('venue_id')->references('id')->on('venues');
            $t->timestamps();
        });
    }

    public function down()
    {
        \Schema::drop('events');
    }
}