<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncidentTable extends Migration
{
    public function up()
    {
        \Schema::create('incidents', function(Blueprint $t)
        {
            $t->bigIncrements('id');
            $t->string('title', 255);
            $t->longText('description');
            $t->mediumText('link')->nullable();
            $t->decimal('latitude')->nullable();
            $t->decimal('longitude')->nullable();
            $t->json('authors');
            $t->longText('comments')->nullable();
            $t->dateTime('date');
            $t->json('extended_details')->nullable();
            $t->json('weather_conditions')->nullable();

            $t->timestamps();
        });
    }

    public function down()
    {
        \Schema::drop('incidents');
    }
}