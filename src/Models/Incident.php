<?php

namespace Adewra\TrafficScotland;

use Carbon\Carbon;
use GeoJson\Geometry\Point;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $table = 'incidents';

    protected $fillable = [
        'title',
        'description',
        'content',
        'permalink',
        'latitude',
        'longitude',
        'authors',
        'comments',
        'date'
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

    public function information()
    {
        return $this->hasOne(IncidentInformation::class, 'id','information');
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::parse($value);
    }

    public function setLocationAttribute($value)
    {
        $this->attributes['location'] = new Point([$this->latitude, $this->longitude]);
    }

    public function getLocationAttribute()
    {
        return isset($this->attributes['location']) ? $this->attributes['location'] : null;
    }
}