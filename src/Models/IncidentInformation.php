<?php

namespace Adewra\TrafficScotland;

use Illuminate\Database\Eloquent\Model;

class IncidentInformation extends Model
{
    protected $table = 'incidents_information';

    protected $fillable = [
        'timestamp',
        'location',
        'direction',
        'type',
        'description',
    ];

    protected $dates = [
        'timestamp'
    ];
}