<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncidentInformationTable extends Migration
{
    public function up()
    {
        Schema::create('incidents_information', function(Blueprint $t)
        {
            $t->increments('id')->unsigned()->autoIncrement();
            $t->unsignedInteger('incident');
            $t->text('location');
            $t->text('direction');
            $t->text('type');
            $t->timestamp('timestamp');

            $t->primary('id');
            $t->foreign('incident')->references('id')->on('incidents');
            $t->onDelete('cascade');
            $t->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('incidents_information');
    }
}