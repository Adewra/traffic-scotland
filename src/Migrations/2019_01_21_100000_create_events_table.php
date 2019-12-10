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
            $t->dateTime('startDateTime');
            $t->dateTime('endDateTime');
            $t->mediumText('description');
            $t->smallInteger('historicAttendance')->nullable();
            $t->dateTime('lastUpdated');
            $t->boolean('isCancelled');
            $t->boolean('isCurrent');
            $t->unsignedBigInteger('venueId');

            //$t->foreign('venueId')->references('identifier')->on('venues');
            $t->timestamps();
        });
    }

    public function down()
    {
        \Schema::drop('events');
    }
}