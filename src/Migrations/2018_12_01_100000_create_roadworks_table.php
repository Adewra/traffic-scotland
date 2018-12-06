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
            $t->longText('delay_information')->nullable();
            $t->longText('works')->nullable();
            $t->longText('traffic_management')->nullable();

            $t->timestamps();
        });
    }

    public function down()
    {
        \Schema::drop('roadworks');
    }
}