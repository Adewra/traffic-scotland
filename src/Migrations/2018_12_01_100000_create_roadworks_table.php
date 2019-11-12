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

            $t->string('identifier', 255);
            $t->string('source', 32);

            $t->string('locationName', 255);
            $t->multiLineString('description')->nullable();
            $t->string('whenType')->nullable();
            $t->string('weekDays')->nullable();
            $t->string('extraLocationDetails')->nullable();
            $t->unsignedInteger('locationX')->nullable();
            $t->unsignedInteger('locationY')->nullable();
            $t->dateTime('endDateTime')->nullable();
            $t->dateTime('startDateTime')->nullable();
            $t->dateTime('weekCommencing')->nullable();
            $t->string('directionText')->nullable();
            $t->string('pressReleaseText')->nullable();
            $t->decimal('latitude')->nullable();
            $t->decimal('longitude')->nullable();
            $t->boolean('isOnHomePage')->nullable();
            $t->json('affectedWeeks')->nullable();

            $t->timestamps();
        });
    }

    public function down()
    {
        \Schema::drop('roadworks');
    }
}