<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncidentsTable extends Migration
{
    public function up()
    {
        \Schema::create('incidents', function(Blueprint $t)
        {
            $t->bigIncrements('id');

            $t->string('identifier', 255);
            $t->string('source', 32);

            $t->dateTime('date');
            $t->string('incidentTypeName', 255);
            $t->dateTime('startTime');
            $t->dateTime('endTime');
            $t->string('locationName', 255);
            $t->longText('description');
            $t->string('directionName', 255);
            $t->string('delay', 255)->nullable();
            $t->string('cause', 255)->nullable();
            $t->string('realWorldLocation', 255)->nullable(); // Might be the wrong data type here, we shall find out once its been used and recorded
            $t->string('diversion', 255)->nullable();
            $t->string('expectedDuration', 255)->nullable();
            $t->string('imageFileName', 255)->nullable();
            $t->unsignedInteger('imageHeight');
            $t->unsignedInteger('imageWidth');
            $t->unsignedInteger('locationX')->nullable();
            $t->unsignedInteger('locationY')->nullable();
            $t->string('title', 255)->nullable();
            $t->unsignedInteger('routeId');
            $t->string('routeName', 255);
            $t->unsignedInteger('incidentTypeId');
            $t->unsignedInteger('incidentSubTypeId');
            $t->string('incidentSubTypeName', 255);
            $t->unsignedInteger('regionId');
            $t->string('regionName', 255);
            $t->dateTime('lastModified');
            $t->json('incidentPoints');
            $t->decimal('latitude')->nullable();
            $t->decimal('longitude')->nullable();

            $t->timestamps();
        });
    }

    public function down()
    {
        \Schema::drop('current_incidents');
    }
}