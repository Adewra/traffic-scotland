<?php

namespace Adewra\TrafficScotland;

use Carbon\Carbon;
use GeoJson\Geometry\Point;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $table = 'incidents';
    protected $primaryKey = 'id';
    protected $increments = true;

    protected $fillable = [
        'identifier',
        'source',
        'date',
        'incidentTypeName',
        'startTime',
        'endTime',
        'locationName',
        'description',
        'directionName',
        'delay',
        'cause',
        'realWorldLocation',
        'diversion',
        'expectedDuration',
        'imageFileName',
        'imageHeight',
        'imageWidth',
        'locationX',
        'locationY',
        'title',
        'routeId',
        'routeName',
        'incidentTypeId',
        'incidentSubTypeId',
        'incidentSubTypeName',
        'regionId',
        'regionName',
        'lastModified',
        'incidentPoints',
        'latitude',
        'longitude'
    ];

    protected $casts = [
        'incidentPoints' => 'array'
    ];

    protected $hidden = [];

    protected $appends = [];

    protected $dates = [
        'date',
        'lastModified',
        'startTime',
        'endTime'
    ];

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::parse($value)->toDateString();
    }

    public function setLastModifiedAttribute($value)
    {
        $this->attributes['lastModified'] = Carbon::parse($value)->toDateTimeLocalString();
    }

    public function setStartTimeAttribute($value)
    {
        $this->attributes['startTime'] = Carbon::parse($value)->toDateTimeLocalString();
    }

    public function setEndTimeAttribute($value)
    {
        $this->attributes['endTime'] = Carbon::parse($value)->toDateTimeLocalString();
    }
}