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
        'title',
        'description',
        'link',
        'latitude',
        'longitude',
        'authors',
        'comments',
        'date',
        'extended_details',
        'weather_conditions'
    ];

    protected $casts = [
        'authors' => 'array',
        'latitude' => 'float',
        'longitude' => 'float'
    ];

    protected $hidden = [
        'latitude',
        'longitude'
    ];

    protected $appends = [
        'location'
    ];

    protected $dates = [
        'date'
    ];

    public function extendedDetails()
    {
        return $this->hasOne(IncidentInformation::class, 'id','extended_details');
    }

    public function weatherConditions()
    {
        return $this->hasOne(IncidentInformation::class, 'id','weather_conditions');
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::parse($value)->toDateString();
    }

    public function getDateAttribute($value)
    {
        return $this->attributes['date'];
    }

    public function setLatitudeAttribute($value)
    {
        $this->attributes['latitude'] = $value;
    }

    public function setLongitudeAttribute($value)
    {
        $this->attributes['longitude'] = $value;
    }

    public function setLocationAttribute($value)
    {
        /*$this->attributes['latitude'] = $value[0];
        $this->attributes['longitude'] = $value[1];*/
    }

    public function getLocationAttribute()
    {
        if(isset($this->attributes['latitude']) && isset($this->attributes['longitude']))
            return new Point([$this->latitude, $this->longitude]);
        else
            return null;
    }
}