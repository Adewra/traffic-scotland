<?php

namespace Adewra\TrafficScotland;

use Illuminate\Database\Eloquent\Model;

class IncidentInformation extends Model
{
    protected $table = 'incidents_information';
    protected $primaryKey = 'id';
    protected $increments = true;

    protected $fillable = [
        'timestamp',
        'date',
        'title',
        'start_time',
        'location',
        'direction',
        'type',
        'description',
        'route_name',
        'direction',
        'delay',
        'diversion',
        'expected_duration'
    ];

    protected $dates = [
        'date',
        'start_time',
        'expected_duration',
        'timestamp'
    ];

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::parse($value)->toDateString();
    }

    public function getDateAttribute()
    {
        return $this->attributes['date'];
    }
}