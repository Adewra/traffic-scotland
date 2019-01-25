<?php

namespace Adewra\TrafficScotland;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';
    protected $primaryKey = 'id';
    protected $increments = true;

    protected $fillable = [
        'identifier',
        'name',
        'date',
        'start_date',
        'end_date',
        'link',
        'icon',
        'description',
        'historic_attendance',
        'last_updated_by_provider',
        'venue_id',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'time',
        'end_time' => 'time',
        'last_updated_by_provider' => 'datetime'
    ];

    protected $hidden = [
    ];

    protected $appends = [
    ];

    protected $dates = [
        'date',
        'last_updated_by_provider'
    ];
}