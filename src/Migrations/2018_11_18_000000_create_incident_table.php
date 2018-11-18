<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncidentTable extends Migration
{
    public function up()
    {
        Schema::create('incidents', function(Blueprint $t)
        {
            $t->increments('id')->unsigned();
            $t->text('title', 255);
            $t->text('description');
            $t->text('content');
            $t->text('link');
            $t->decimal('latitude');
            $t->decimal('longitude');
            $t->json('authors');
            $t->text('comments');
            $t->dateTime('date');
            $t->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('incidents');
    }
}