<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncidentTable extends Migration
{
    public function up()
    {
        Schema::create('incidents', function(Blueprint $t)
        {
            $t->increments('id')->unsigned()->autoIncrement();
            $t->text('title', 255);
            $t->text('description');
            $t->text('content');
            $t->text('permalink')->nullable();
            $t->decimal('latitude');
            $t->decimal('longitude');
            $t->json('authors');
            $t->text('comments')->nullable();
            $t->dateTime('date');
            $t->unsignedInteger('information');

            $t->primary('id');
            $t->foreign('information')->references('id')->on('incidents_information');
            $t->onDelete('cascade');
            $t->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('incidents');
    }
}