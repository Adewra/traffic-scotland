<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncidentInformationTable extends Migration
{
    public function up()
    {
        Schema::create('incidents_information', function(Blueprint $t)
        {
            $t->bigIncrements('id');
            $t->unsignedBigInteger('incident');

            $t->string('class');
            $t->timestamp('timestamp');
            $t->date('date');
            $t->string('title');
            $t->dateTime('start_time');
            $t->string('location');
            $t->text('direction');
            $t->text('type');
            $t->longText('description');
            $t->string('route_name');
            $t->string('direction');
            $t->string('delay');
            $t->string('diversion');
            $t->dateTime('expected_duration');

            $t->foreign('incident')->references('id')->on('incidents');
            $t->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('incidents_information');
    }
}