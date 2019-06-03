<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoadworksTable extends Migration
{
    public function up()
    {
        \Schema::create('roadworks', function(Blueprint $t)
        {
            $t->bigIncrements('id');
            $t->string('identifier', 255)->unique();
            $t->string('prefix', 6);
            $t->string('title', 255);
            $t->longText('description');
            $t->mediumText('link')->nullable();
            $t->decimal('latitude')->nullable();
            $t->decimal('longitude')->nullable();
            $t->json('authors')->nullable();
            $t->longText('comments')->nullable();
            $t->dateTime('date');
            $t->date('start_date')->nullable();
            $t->date('end_date')->nullable();
            $t->json('delay_information')->nullable();
            $t->json('works')->nullable();
            $t->json('traffic_management')->nullable();
            $t->json('diversion_information')->nullable();
            $t->json('days_affected')->nullable();
            $t->json('media_release')->nullable();

            $t->timestamps();
        });
    }

    public function down()
    {
        \Schema::drop('roadworks');
    }
}