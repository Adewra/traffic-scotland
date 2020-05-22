<?php

namespace Adewra\TrafficScotland;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Roadwork extends Model
{
    protected $table = 'roadworks';
    protected $primaryKey = 'id';
    protected $increments = true;

    protected $fillable = [
        'identifier',
        'source',
        'locationName',
        'description',
        'whenType',
        'weekDays',
        'extraLocationDetails',
        'locationX',
        'locationY',
        'endDateTime',
        'startDateTime',
        'weekCommencing',
        'directionText',
        'pressReleaseText',
        'latitude',
        'longitude',
        'isOnHomePage',
        'affectedWeeks'
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'description' => 'array',
        'affectedWeeks' => 'array',
        'isOnHomePage' => 'boolean',
        'startDateTime' => 'datetime',
        'endDateTime' => 'datetime',
        'weekCommencing' => 'datetime'
    ];

    protected $hidden = [
        'latitude',
        'longitude'
    ];

    public function setStartDateTimeAttribute($value)
    {
        $this->attributes['startDateTime'] = Carbon::parse($value)->toDateTimeString();
    }

    public function setEndDateTimeAttribute($value)
    {
        $this->attributes['endDateTime'] = Carbon::parse($value)->toDateTimeString();
    }

    public function setWeekCommencingAttribute($value)
    {
        $this->attributes['weekCommencing'] = Carbon::parse($value)->toDateTimeString();
    }
}